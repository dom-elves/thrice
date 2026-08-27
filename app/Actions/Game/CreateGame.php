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
     *
     * @param  array{name: string, password: string}  $data
     */
    public function create(array $data): Game
    {
        return DB::transaction(function () use ($data) {
            $game = Game::create([
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
            ]);

            $userId = auth()->user()->id;

            $this->createGameUser->create($game->id, $userId);

            DB::afterCommit(function () use ($game) {
                Redis::pipeline(function ($pipe) use ($game) {
                    $pipe->hset("game:{$game->id}", 'name', $game->name); // omitting password for now as rather just have it in mysql db
                    $pipe->hset("game:{$game->id}", 'hands', 0);
                    $pipe->hset("game:{$game->id}", 'finished', $game->finished ? '1' : '0');
                    $pipe->hset("game:{$game->id}", 'start', $game->created_at->toDateTimeString());
                });
            });

            return $game;
        });
    }
}
