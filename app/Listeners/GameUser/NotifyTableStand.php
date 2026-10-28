<?php

namespace App\Listeners\GameUser;

use App\Events\GameUser\Stand;
use App\Jobs\Game\CloseGame;
use App\Notifications\GameUser\StandNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class NotifyTableStand implements ShouldQueue
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
    public function handle(Stand $event): void
    {
        $event->gameUser->game->notify(new StandNotification($event->gameUser));
    }
}
