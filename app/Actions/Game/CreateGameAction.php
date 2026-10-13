<?php

namespace App\Actions\Game;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class CreateGameAction
{
    public function __construct() {}

    /**
     * Create a Game.
     */
    public function execute(array $data): Game
    {
        $game = DB::transaction(function () use ($data) {
            return Game::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'password' => $data['password'] === '' ? '' : bcrypt($data['password']),
            ]);
        });

        return $game;
    }
}
