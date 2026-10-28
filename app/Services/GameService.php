<?php

namespace App\Services;

use App\Actions\Game\CreateGameAction;
use App\Events\GameUser\Sit;
use App\Events\GameUser\Stand;
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
     * - call action for creating game
     * - set in redis
     *
     * @param  array<string, string>  $data
     */
    public function create(array $data): Game
    {
        $game = app(CreateGameAction::class)->execute($data);

        Redis::hmset("game:{$game->code}", [
            'name' => $game->name,
            // don't think i need pw here
            'hands' => 0,
            'started' => $game->started,
            'finished' => $game->finished,
            // start time to be done later
        ]);

        // maybe add automatic seating for created game?

        return $game;
    }

    /**
     * Sit at the table of a game.
     * - set game user in redis
     * - update status to in game
     * - broadcast to table
     *
     * @param  GameUser  $gameUser
     */
    public function sit($gameUser): void
    {
        $key = "game_user:{$gameUser->id}";

        Redis::hmset($key, [
            'game_id' => $gameUser->game->id,
            'user_id' => $gameUser->user->id,
            'start_balance' => $gameUser->start_balance,
            'end_balance' => $gameUser->end_balance,
            'join_time' => Carbon::now()->toDateTimeString(),
            'leave_time' => '',
            'in_game' => 1,
            'user_session_id' => session()->getId(),
        ]);

        // i can remove this if i change it in middleware
        Redis::sadd("game:{$gameUser->game->id}:game_user_ids", $gameUser->id);

        $gameUser->update([
            'in_game' => true,
        ]);

        event(new Sit($gameUser));
    }

    /**
     * Stand at the table of a game. Just the inverse of sit()
     * - remove user from redis
     * - update in game status to false
     * - broadcast to table
     *
     * @param  GameUser  $gameUser
     */
    public function stand($gameUser): void
    {
        // this will eventually need to include a bunch of logic for game state
        // but for now, just as if the user is leaving the game without doing anything

        $key = "game_user:{$gameUser->id}";

        Redis::hgetdel($key, [
            'game_id',
            'user_id',
            'start_balance',
            'end_balance',
            'join_time',
            'leave_time',
            'in_game',
            'user_session_id',
        ]);

        Redis::srem("game:{$gameUser->game->id}:game_user_ids", $gameUser->id);

        $gameUser->update([
            // balance incr
            'in_game' => false,
        ]);

        event(new Stand($gameUser));
    }
}
