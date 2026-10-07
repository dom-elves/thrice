<?php

namespace App\Services;

use App\Enums\Rank;
use App\Enums\Suit;
use App\Events\Hand\HandDealt;

class HandService
{
    public function deal($gameUsers): void
    {
        $deck = [];

        foreach (Suit::cases() as $suit) {
            foreach (Rank::cases() as $rank) {
                $deck[] = $rank->value . $suit->value;
            }
        }

        $deck = collect($deck)->shuffle();
        $players = $gameUsers->count();
        $cardsPerPlayer = 6;

        // rather than deal card by card like in poker and nest a for loop
        // just take 6 cards off the top each time
        for ($i = 0; $i < $players; $i++) {
            $hand = $deck->splice(0, $cardsPerPlayer);
            $gameUsers[$i]->hand = $hand;
        }

        foreach ($gameUsers as $game_user) {
            broadcast(new HandDealt($game_user));
        }
    }
}