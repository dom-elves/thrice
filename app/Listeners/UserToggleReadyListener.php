<?php

namespace App\Listeners;

use App\Events\Lobby\UserToggleReady;

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
