<?php

namespace App\Actions\Game;

use App\Models\GameUser;
use App\Services\GameService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CreateGameUserAction
{
    public function __construct(
        private GameService $gameService,
    ) {}
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

            DB::afterCommit(fn () => $this->gameService->joinGame($gameUser));

            return $gameUser;
        });
    }
}
