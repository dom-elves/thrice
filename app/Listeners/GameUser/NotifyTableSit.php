<?php

namespace App\Listeners\GameUser;

use App\Events\GameUser\Sit;
use App\Notifications\SitNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTableSit implements ShouldQueue
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
    public function handle(Sit $event): void
    {
        $event->gameUser->game->notify(new SitNotification($event->gameUser));
    }
}
