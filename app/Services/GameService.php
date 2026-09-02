<?php

namespace App\Services;

use App\Events\GameUserJoined;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

/**
 * Service layer for handing game logic, basically anything that interacts with Redis.
 * - Joining
 * - Leaving
 * - gameplay logic, tbc
 */
class GameService 
{
    public function createGame($game)
    {
        Redis::pipeline(function ($pipe) use ($game) {
            $pipe->hset("game:{$game->id}", 'name', $game->name); // omitting password for now as rather just have it in mysql db
            $pipe->hset("game:{$game->id}", 'hands', 0);
            $pipe->hset("game:{$game->id}", 'finished', $game->finished ? '1' : '0');
            $pipe->hset("game:{$game->id}", 'start', $game->created_at->toDateTimeString());
        });
    }

    public function joinGame($gameUser)
    {
        Redis::pipeline(function ($pipe) use ($gameUser) {
            $pipe->hset("game_user:{$gameUser->id}", 'game_id', $gameUser->game->id);
            $pipe->hset("game_user:{$gameUser->id}", 'user_id', $gameUser->user->id);
            $pipe->hset("game_user:{$gameUser->id}", 'start_balance', $gameUser->start_balance);
            $pipe->hset("game_user:{$gameUser->id}", 'end_balance', $gameUser->end_balance);
            $pipe->hset("game_user:{$gameUser->id}", 'join_time', Carbon::now()->toDateTimeString());
            $pipe->hset("game_user:{$gameUser->id}", 'leave_time', null);
        });

        event(new GameUserJoined($gameUser));
    }
}
