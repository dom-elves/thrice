<?php

namespace App\Actions\Game;

use App\Jobs\Lobby\CloseLobby;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CreateGameAction
{
    public function __construct() {}

    /**
     * Create a Game.
     */
    public function execute(string $code): Game
    {
        $data = Redis::hgetall("game:{$code}");

        $game = DB::transaction(function () use ($data) {
            return Game::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'password' => $data['password'] === '' ? '' : bcrypt($data['password']),
            ]);
        });

        CloseLobby::dispatch($code);

        return $game;
    }
}
