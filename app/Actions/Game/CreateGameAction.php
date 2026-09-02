<?php

namespace App\Actions\Game;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Support\Facades\DB;

class CreateGameAction
{
    public function __construct(
        private CreateGameUserAction $createGameUserAction,
        private GameService $gameService,
    ) {}

    /**
     * Create a Game.
     *
     * @param  array{name: string, password?: string}  $data
     */
    public function execute(array $data): Game
    {
        return DB::transaction(function () use ($data) {

            $game = Game::create([
                'name' => $data['name'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : '',
            ]);

            DB::afterCommit(fn () => $this->gameService->createGame($game));

            $userId = auth()->user()->id;

            $this->createGameUserAction->create($game->id, $userId);

            return $game;
        });
    }
}
