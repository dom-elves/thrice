<?php

namespace App\Services;

use App\Models\Game;
use App\Enums\Rank;
use App\Enums\Suit;
use App\ValueObjects\Card;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class DeckService
{
    public function __construct() {

    }

    /**
     * Builds the deck using enums for Rank & Case
     * basically a safer version of looping within a loop
     *
     * @return Collection<int, Card>
     *
     */
    public function build(): Collection
    {
        $deck = collect(Rank::cases())
            ->crossJoin(Suit::cases())
            ->map(fn ($pair) => new Card(...$pair));
        
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
                'cards'  => $deck->splice(0, 6)->values(),
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
        }

        $bottom = $deck->splice(0, 3);
        Redis::hmset("bottom:{$game->code}", [
            'bottom_flop_1' => $bottom[0]->shorthand,
            'bottom_flop_2' => $bottom[1]->shorthand,
            'bottom_flop_3' => $bottom[2]->shorthand,
        ]);

        $middle = $deck->splice(0, 3);
        Redis::hmset("middle:{$game->code}", [
            'middle_flop_1' => $middle[0]->shorthand,
            'middle_flop_2' => $middle[1]->shorthand,
            'middle_flop_3' => $middle[2]->shorthand,
        ]);

        $top = $deck->splice(0, 3);
        Redis::hmset("top:{$game->code}", [
            'top_flop_1' => $top[0]->shorthand,
            'top_flop_2' => $top[1]->shorthand,
            'top_flop_3' => $top[2]->shorthand,
        ]);
    }
}