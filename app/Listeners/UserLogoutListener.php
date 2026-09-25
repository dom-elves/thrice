<?php

namespace App\Listeners;

use App\Services\LobbyService;
use Illuminate\Auth\Events\Logout;

class UserLogoutListener
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
    public function handle(Logout $event): void
    {
        $user = $event->user;

        $lobby = session('lobby_code');

        if ($lobby) {
            $lobbyService = app(LobbyService::class);
            $lobbyService->leave($user, $lobby);
        }

        // do the same for game eventually
        // todo: use session('lobby_code') for middleware next
    }
}
