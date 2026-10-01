<?php

namespace App\Actions\Game;

use App\Jobs\CloseLobby;
use App\Jobs\Game\CreateGameUser;
use App\Models\Game;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

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

        $user_ids = Redis::smembers("lobby:{$code}:user_ids");

        $jobs = collect($user_ids)->map(fn ($user_id) => new CreateGameUser($game->id, $user_id));

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) {
                // maybe do somethng here in the future
            })->catch(function (Batch $batch, Throwable $e) {
                // do something here eventually
            })->finally(function () use ($code) {
                CloseLobby::dispatch($code);
            })->dispatch();

        return $game;
    }
}
