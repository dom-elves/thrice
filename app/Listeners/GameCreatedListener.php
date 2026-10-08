<?php

namespace App\Listeners;

use App\Events\GameCreated;
use App\Services\HandService;

class GameCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(public HandService $handService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GameCreated $event): void
    {
        $this->handService->deal($event->game->gameUsers);
    }
}
