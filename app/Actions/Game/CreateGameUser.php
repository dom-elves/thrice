<?php

namespace App\Actions\Game;

use App\Events\GameUserCreated;
use App\Models\GameUser;
use Illuminate\Support\Facades\DB;

class CreateGameUser
{
    /**
     * Create a GameUser when the user wishes to join a given game.
     */
    public function create(int $gameId, int $userId): GameUser
    {
        return DB::transaction(function () use ($gameId, $userId) {
            $gameUser = GameUser::create([
                'game_id' => $gameId,
                'user_id' => $userId,
                'start_balance' => 1000,
            ]);

            event(new GameUserCreated($gameUser));

            return $gameUser;
        });
    }
}
