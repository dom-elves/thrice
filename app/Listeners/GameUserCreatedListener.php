<?php

namespace App\Listeners;

use App\Events\GameUserCreated;
use App\Notifications\GameUserJoinedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class GameUserCreatedListener implements ShouldQueue
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
    public function handle(GameUserCreated $event): void
    {
        $event->gameUser->game->notify(new GameUserJoinedNotification($event->gameUser));
    }
}
