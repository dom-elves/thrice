<?php

namespace App\Listeners;

use App\Events\GameUserLeft;
use App\Notifications\GameUserLeftNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $event->gameUser->game->notify(new GameUserLeftNotification($event->gameUser));
    }
}
