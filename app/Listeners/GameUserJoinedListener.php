<?php

namespace App\Listeners;

use App\Events\GameUserJoined;
use App\Notifications\GameUserJoinedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class GameUserJoinedListener implements ShouldQueue
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
    public function handle(GameUserJoined $event): void
    {
        $event->gameUser->game->notify(new GameUserJoinedNotification($event->gameUser));
    }
}
