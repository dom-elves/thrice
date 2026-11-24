<?php

namespace App\Services;

use App\Enums\Rank;
use App\Enums\Suit;
use App\Events\Game\DealBoard;
use App\Events\GameUser\Hand;
use App\Models\Game;
use App\ValueObjects\Card;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class DeckService
{
    public function __construct() {}

    /**
     * Builds the deck using enums for Rank & Case
     * basically a safer version of looping within a loop
     *
     * @return Collection<int, Card>
     */
    public function build(): Collection
    {
        $deck = collect(Rank::cases())
            ->crossJoin(collect(Suit::cases()))
            /** @param array{0: Rank, 1: Suit} $pair */
            // @phpstan-ignore argument.type
            ->map(fn (array $pair) => new Card($pair[0], $pair[1]));

        return $deck;
    }

    /**
     * Deal the cards per player in the game
     * - take the seats w/ids from redis
     * - build & shuffle the deck
     * - filter by filled seats, then map cards to players, taking the top 6 cards each time
     * - loop over the seats, set the card shorthands in redis
     */
    public function deal(Game $game): void
    {
        $seats = collect(Redis::hgetall("game_seats:{$game->code}"));
        $deck = $this->build()->shuffle();

        // seated players & their cards
        $seats = $seats->filter(fn ($gameUserId) => filled($gameUserId))
            ->map(fn ($gameUserId) => [
                'gameUserId' => $gameUserId,
                'cards' => $deck->splice(0, 6)->values(),
            ]);

        // adding cards to each game user hash
        foreach ($seats as $seat) {
            $gameUserId = $seat['gameUserId'];
            $cards = $seat['cards'];

            Redis::hmset("game_user:{$gameUserId}", [
                'bottom_card_1' => $cards[0]->shorthand,
                'bottom_card_2' => $cards[1]->shorthand,
                'middle_card_1' => $cards[2]->shorthand,
                'middle_card_2' => $cards[3]->shorthand,
                'top_card_1' => $cards[4]->shorthand,
                'top_card_2' => $cards[5]->shorthand,
            ]);

            event(new Hand($gameUserId, $cards->pluck('shorthand')->toArray()));
        }

        $rows = ['bottom', 'middle', 'top'];

        foreach ($rows as $row) {
            $cards = $deck->splice(0, 5);

            Redis::hmset("$row:{$game->code}", [
                'flop_1' => $cards[0]->shorthand,
                'flop_2' => $cards[1]->shorthand,
                'flop_3' => $cards[2]->shorthand,
                'turn' => $cards[3]->shorthand,
                'river' => $cards[4]->shorthand,
            ]);

            $board[] = [$row => $cards->pluck('shorthand')->toArray()];
        }
        // @phpstan-ignore argument.type
        event(new DealBoard($game, $board));
    }

    /**
     * Locks the player's hand in the order they've set.
     */
    public function lockHand(array $cards, string $gameUserId): void
    {
        Redis::hmset("game_user:{$gameUserId}", $cards);
    }
}
