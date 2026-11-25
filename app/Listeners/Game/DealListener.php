<?php

namespace App\Listeners\Game;

use App\Events\Game\Deal;
use App\Services\DeckService;

class DealListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(Deal $event): void
    {
        (new DeckService($event->game))->deal();
    }
}
