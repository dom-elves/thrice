<?php

namespace App\Actions\Game;

use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CreateGame
{
    public function __construct(
        private CreateGameUser $createGameUser
    ) {}

    /**
     * Create a Game.
     */
    public function create($data): Game
    {
        return DB::transaction(function () use ($data) {
            $game = Game::create([
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
            ]);

            $userId = auth()->user()->id;

            $this->createGameUser->create($game->id, $userId);

            DB::afterCommit(function () use ($game) {
                Redis::hset("game:{$game->id}", [
                    'name' => $game->name,
                    // i think not to store the game pw in redis, keep that in mysql
                    'hands' => 0,
                    'finished' => $game->finished,
                    'start' => $game->created_at->toDateTimeString(),
                ]);
            });

            return $game;
        });
    }
}
