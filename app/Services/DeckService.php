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
    public array $rows;

    public function __construct(
        protected readonly Game $game
    )
    {
        $this->rows = ['bottom', 'middle', 'top'];
    }

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
    public function deal(): void
    {
        $seats = collect(Redis::hgetall("game_seats:{$this->game->code}"));
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

        foreach ($this->rows as $row) {
            $cards = $deck->splice(0, 5);

            Redis::hmset("$row:{$this->game->code}", [
                'flop_1' => $cards[0]->shorthand,
                'flop_2' => $cards[1]->shorthand,
                'flop_3' => $cards[2]->shorthand,
                'turn' => $cards[3]->shorthand,
                'river' => $cards[4]->shorthand,
            ]);

            $board[] = [$row => $cards->pluck('shorthand')->toArray()];
        }
        // @phpstan-ignore argument.type
        event(new DealBoard($this->game, $board));
    }

    /**
     * Locks the player's hand in the order they've set.
     */
    public function lockHand(array $cards, string $gameUserId): void
    {
        Redis::hmset("game_user:{$gameUserId}",
            array_merge($cards, ['hand_locked' => true]
        ));

        foreach ($this->rows as $row) {
            $pairs[$row] = [
                $cards["{$row}_card_1"], 
                $cards["{$row}_card_2"]
            ];
            // dd(collect($pairs[$row]), $row);
            $hand[] = $this->calcHand(collect($pairs[$row]), $row);
        }

        dd($hand);
    }

    /**
     * Calculate a player's hand, for a given row.
     * - build a deck
     * - filter by player hand to retrieve card objects
     * - filter by given row to retrieve card object
     * 
     * - evaluate potential hands out of the 7 cards, ordered in asc
     */
    private function calcHand(Collection $cards, string $row): Collection
    {
        $deck = $this->build();

        $handCards = $deck->filter(fn (Card $card) => $cards->contains($card->shorthand));
        $hand = '';
        $row = collect(Redis::hgetall("{$row}:{$this->game->code}"));
        $rowCards = $deck->filter(fn (Card $card) => $row->contains($card->shorthand));
        
        $cards = $handCards->merge($rowCards)->sortBy(fn (Card $card) => $card->rank->value);
        
        // as royal flush is the highest ranked hand
        // we check that & similar (straight and normal flush) hands too
        $flushCards = $cards->groupBy(fn (Card $card) => $card->suit->name)
            ->first(fn ($group) => $group->count() >= 5);
        
        if ($flushCards) {
            // if there's 6 or 7 cards in the flush, only take the top 5
            if ($flushCards->count() >= 6) {
                $flushCards = $flushCards->take(-5);
            }

            // using chunks, check whether the cards are consecutive
            $consecutiveCards = $flushCards->sort()->values()->chunkWhile(
                fn ($value, $key, $chunk) => $value === $chunk->last()->rank->value + 1
            )->count() === 1;

            // if they are, it's a royal or straight flush
            if ($consecutiveCards) {
                // last card being an ace is a royal flush
                // otherwise, it's a straight flush
                if ($flushCards->last()->rank === Rank::Ace) {
                    $hand = 'royal flush';
                } else {
                    $hand = 'straight flush';
                }
            } else {
                // so if it's just 5 of the same suit, it's just a flush
                $hand = 'flush';
            }
        }

        if (!$flushCards) {
            $quads = $cards->groupBy(fn (Card $card) => $card->rank->value)
                ->first(fn ($group) => $group->count() == 4);

            if ($quads) {
                $kicker = $cards->reject(
                    fn (Card $card) => $card->rank->value == $quads[0]->rank->value
                )->last();
                
                // remember to return kicker
                $hand = 'quads';
            }

            $trips = $cards->groupBy(fn (Card $card) => $card->rank->value)
                ->filter(fn ($group) => $group->count() === 3)
                ->values();

            // this covers the condition that a player can have two trips
            // which isn't a valid hand, but must be treated differently to a normal house
            if ($trips->count() === 2) {
                
                $pair = $trips[0]->take(2);
                // hand will be [$trips[1], $pair];
                $hand = 'house';
            } 

            if ($trips->count() === 1) {
                $kickers = $cards->reject(
                    fn (Card $card) => $card->rank->value == $trips[0][0]->rank->value
                )->take(-2);

                // hand will be [$trips[0], $kickers];
                if ($kickers->unique()->count === 1) {
                    $hand = 'house';
                } else {
                    $hand = 'trips';
                }
            }

            // this is broken
            // // straight
            // $cardValues = $cards->map(fn (Card $card) => $card->rank->value);
            
            // // if there is an ace, push a 1 so it can play low
            // if ($cardValues->contains(14)) {
            //     $cardValues->unshift(1);
            // }

            // // last means it will awlays play the highest straight, 
            // // if there are 6 or 7 consecutive cards
            // $straightValues = $cardValues->unique()->sort()->values()
            //     ->sliding(5)
            //     ->last(fn ($window) => $window->last() - $window->first() === 4);

            // $straight = $straightValues->map(fn ($value) => 
            //     $cards->first(fn (Card $card) => 
            //         $card->rank->value === $value));

            // $hand = 'straight';

            
            $pairs = $cards->groupBy(fn (Card $card) => $card->rank->value)
                ->filter(fn ($group) => $group->count() === 2)
                ->values();
            
            // covers the condition where a player can have 3 pairs, 
            // as only two are playable
            if ($pairs->count() >= 2) {
                $topTwo = $pairs->take(-2);

                $twoPairCards = $topTwo->first()->merge($topTwo->last());

                $kicker = $cards->reject(
                    fn (Card $card) => $twoPairCards->contains(
                        fn (Card $c) => $c->rank === $card->rank
                    )
                )->last();

                // in a two pair, there is 1 kicker
                $hand = 'two pair';
            }

            if ($pairs->count() == 1) {
                $pair = $pairs[0];

                $kickers = $cards->reject(
                    fn (Card $card) => $pair->contains(
                        fn (Card $c) => $c->rank === $card->rank
                    )
                )->take(-3);

                // top 3 cards as kickers for pair
                $hand = 'pair';
            }

            if ($hand === '') {
                $handCards = $cards->take(-5);
                $hand = 'high card';
            }
        }

        return $hand;
    }

    private function checkRoyalFlush()
    {
        
    }
}
