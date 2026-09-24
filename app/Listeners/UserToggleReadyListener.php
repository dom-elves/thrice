<?php

namespace App\Listeners;

use App\Events\Lobby\UserToggleReady;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     */
    public function handle(UserToggleReady $event): void
    {
        // redis, etc to see if game should beghin
    }
}
