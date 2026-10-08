<?php

namespace App\Services;

use App\Enums\Rank;
use App\Enums\Suit;
use App\Events\Hand\HandDealt;
use App\Models\GameUser;
use App\Notifications\GameUserReadyNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class HandService
{
    /**
     * @param Collection<int, GameUser> $gameUsers
     */
    public function deal(Collection $gameUsers): void
    {
        $deck = [];

        foreach (Suit::cases() as $suit) {
            foreach (Rank::cases() as $rank) {
                $deck[] = $rank->value.$suit->value;
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

    public function ready(GameUser $gameUser): void
    {
        $key = "game:{$gameUser->game->code}:ready_user_ids";

        Redis::sadd($key, $gameUser->user->id);

        $gameUser->game->notify(new GameUserReadyNotification($gameUser));

        Redis::hmset("game_user:{$gameUser->id}", [
            'is_ready' => 1,
        ]);

        if (Redis::scard($key) === 6) {
            // start new round
        }
    }
}
