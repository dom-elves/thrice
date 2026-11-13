<?php

namespace App\Listeners\Game;

use App\Events\Game\Deal;
use App\Services\DeckService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class DealListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     * 
     */
    public function handle(Deal $event): void
    {
        app(DeckService::class)->deal($event->game);
    }
}
