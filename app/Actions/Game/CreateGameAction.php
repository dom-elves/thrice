<?php

namespace App\Actions\Game;

use App\Actions\Game\CreateGameUserAction;
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

        $user_ids = Redis::smembers("lobby:{$code}:ready_user_ids");

        // gonna have this as a temporary thing in place as it just works
        // but i am aware it is not particularly efficient
        // though since it's max 6 records it shouldn't really matter
        foreach ($user_ids as $user_id) {
            app(CreateGameUserAction::class)->execute($game->id, $user_id);
        }

        CloseLobby::dispatch($code);
        
        return $game;
    }
}
