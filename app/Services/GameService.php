<?php

namespace App\Services;

use App\Events\GameUserJoined;
use App\Events\GameUserLeft;
use App\Models\Game;
use App\Models\GameUser;
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
    /**
     * Create an instance of the game in Redis
     *
     * @param  Game  $game
     */
    public function createGame($game): void
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

    /**
     * Join a game, set the game user in Redis and broadcast the join event to the fe
     *
     * @param  GameUser  $gameUser
     */
    public function joinGame($gameUser): void
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

        Redis::sadd("game:{$gameUser->game->id}:game_user_ids", $gameUser->id);

        $gameUser->update([
            'in_game' => true,
        ]);

        event(new GameUserJoined($gameUser));
    }

    /**
     * Leave a game, set the game user in Redis and broadcast the leave event to the fe
     *
     * @param  GameUser  $gameUser
     */
    public function leaveGame($gameUser): void
    {
        // this will eventually need to include a bunch of logic for game state
        // but for now, just as if the user is leaving the game without doing anything

        $key = "game_user:{$gameUser->id}";
        $state = Redis::hgetall($key);

        Redis::pipeline(function ($pipe) use ($gameUser) {
            $pipe->hgetdel("game_user:{$gameUser->id}", [
                'game_id',
                'user_id',
                'start_balance',
                'end_balance',
                'join_time',
                'leave_time',
                'in_game',
            ]);
        });

        Redis::srem("game:{$gameUser->game->id}:game_user_ids", $gameUser->id);

        $gameUser->update([
            // balance incr
            'in_game' => false,
        ]);

        event(new GameUserLeft($gameUser));

        if (! Redis::exists("game:{$gameUser->game->id}:game_user_ids")) {
            dd('games gone');
        } else {
            dd('games back');
        }

        // todo: destroy game job
        // - check mysql game->players or w/e
        // - destroy game, containing logic to take down redis users too
        // doesn't need to happen instantly, hence job
    }
}
