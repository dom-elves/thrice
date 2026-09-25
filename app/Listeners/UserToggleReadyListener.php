<?php

namespace App\Listeners;

use App\Events\GameCreated;
use App\Events\Lobby\UserToggleReady;
use App\Services\GameService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UserToggleReadyListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     */
    public function handle(UserToggleReady $event): void
    {
        $allReady = empty(Redis::sdiff(
            "lobby:{$event->code}:user_ids", 
            "lobby:{$event->code}:ready_user_ids"
        ));

        $playerCount = Redis::scard("lobby:{$event->code}:ready_user_ids");

        if ($allReady && $playerCount >= 2) {
            $lock = Cache::lock("lobby:{$event->code}:game-creating", 10);

            if ($lock->get()) {
                $gameService = app(GameService::class);
                $game = $gameService->create($event->code);

                DB::afterCommit(function () use ($game) {
                    $game->update([
                        'started' => true
                    ]);

                    broadcast(new GameCreated($game));
                });

                $lock->release();
            }
        }
    }
}
