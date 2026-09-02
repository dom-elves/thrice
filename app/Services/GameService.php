<?php

namespace App\Services;

use App\Events\GameUserJoined;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

/**
 * Service layer for handing game logic, basically anything that interacts with Redis.
 * - Game creation
 * - Joining
 * - Leaving
 * - gameplay logic, tbc
 */
class GameService
{
    public function createGame($game)
    {
        Redis::pipeline(function ($pipe) use ($game) {
            $pipe->hmset("game:{$game->id}", [
                'name' => $game->name,
                'hands' => 0,
                'finished' => $game->finished ? '1' : '0',
                'start' => $game->created_at->toDateTimeString(),
            ]);
        });
    }

    public function joinGame($gameUser)
    {
        Redis::pipeline(function ($pipe) use ($gameUser) {
            $pipe->hmset("game_user:{$gameUser->id}", [
                'game_id' => $gameUser->game->id,
                'user_id' => $gameUser->user->id,
                'start_balance' => $gameUser->start_balance,
                'end_balance' => $gameUser->end_balance,
                'join_time' => Carbon::now()->toDateTimeString(),
                'leave_time' => '',
                'in_game' => 1,
            ]);
        });

        $gameUser->update([
            'in_game' => true,
        ]);

        event(new GameUserJoined($gameUser));
    }

    public function leaveGame($gameUser)
    {
        // this will eventually need to include a bunch of logic for game state
        // but for now, just as if the user is leaving the game without doing anything

        $key = "game_user:{$gameUser->id}";

        Redis::pipeline(function ($pipe) use ($gameUser) {
            $pipe->hmset("game_user:{$gameUser->id}", [
                'leave_time' => Carbon::now()->toDateTimeString(),
            ]);
        });

        $state = Redis::hgetall($key);
        dd($state);
        $gameUser->update([
            // balance incr
            //
        ]);
    }
}
