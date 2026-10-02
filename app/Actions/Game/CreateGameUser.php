<?php

namespace App\Actions\Game;

use App\Models\GameUser;
use Illuminate\Support\Facades\DB;

class CreateGameUser
{
    public function __construct() {}

    /**
     * Create a GameUser.
     */
    public function execute(int $gameId, int $userId): GameUser
    {
        return DB::transaction(function () use ($gameId, $userId) {
            return GameUser::create([
                'game_id' => $gameId,
                'user_id' => $userId,
                'start_balance' => 1000,
            ]);
        });
    }
}
