<?php

namespace App\Actions\Game;

use App\Models\Game;
use App\Services\GameService;
use App\Services\GameUserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CreateGameAction
{
    public function __construct(
        private CreateGameUserAction $createGameUserAction,
    ) {}

    /**
     * Create a Game.
     *
     * @param  string $code
     */
    public function execute(string $code): Game
    {
        return DB::transaction(function () use ($code) {
            $data = Redis::hgetall("game:{$code}");

            $game = Game::create($data);
            dd($game);
            // DB::afterCommit(fn () => $this->gameService->createGame($game));

            $userId = auth()->user()->id;

            $this->createGameUserAction->execute($game->id, $userId);

            return $game;
        });
    }
}
