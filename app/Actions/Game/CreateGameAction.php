<?php

namespace App\Actions\Game;

use App\Jobs\CloseLobby;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CreateGameAction
{
    public function __construct(
        private CreateGameUserAction $createGameUserAction,
    ) {}

    /**
     * Create a Game.
     */
    public function execute(string $code): Game
    {
        $data = Redis::hgetall("game:{$code}");

        $game = DB::transaction(function () use ($data) {
            return Game::create($data);
        });

        // destroy lobby, should use a event+listener but this is the only place that game creation will be
        CloseLobby::dispatch($code);

        return $game;
    }
}
