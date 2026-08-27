<?php

namespace App\Actions\Game;

use App\Events\GameUserCreated;
use App\Models\GameUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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

            DB::afterCommit(function () use ($gameUser) {
                Redis::pipeline(function ($pipe) use ($gameUser) {
                    $pipe->hset("game_user:{$gameUser->id}", 'game_id', $gameUser->game->id);
                    $pipe->hset("game_user:{$gameUser->id}", 'user_id', $user->id);
                    $pipe->hset("game_user:{$gameUser->id}", 'start_balance', $gameUser->start_balance);
                    $pipe->hset("game_user:{$gameUser->id}", 'end_balance', $gameUser->end_balance);
                    $pipe->hset("game_user:{$gameUser->id}", 'join_time', Carbon::now()->toDateTimeString());
                    $pipe->hset("game_user:{$gameUser->id}", 'leave_time', null);
                });
            });

            return $gameUser;
        });
    }
}
