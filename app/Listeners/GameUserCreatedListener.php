<?php

namespace App\Listeners;

use App\Events\GameUserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\GameUserJoinedNotification;

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
