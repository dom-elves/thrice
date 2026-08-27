<?php

namespace App\Actions\Game;

use App\Actions\Game\CreateGameUser;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

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

            return $game;
        });    
    }
}
