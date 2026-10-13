<?php

namespace App\Listeners;

use App\Events\GameUserLeft;
use App\Jobs\CloseGame;
use App\Notifications\GameUserLeftNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class GameUserLeftListener implements ShouldQueue
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
     */
    public function handle(GameUserLeft $event): void
    {
        $game = $event->gameUser->game;

        if (! Redis::exists("lobby:{$game->code}:game_user_ids")) {
            CloseGame::dispatch($game->id);
        } else {
            $game->notify(new GameUserLeftNotification($event->gameUser));
        }
    }
}
