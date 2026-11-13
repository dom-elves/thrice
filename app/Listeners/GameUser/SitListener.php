<?php

namespace App\Listeners\GameUser;

use App\Events\GameUser\Sit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class SitListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(Sit $event): void
    {
        $game = Redis::hgetall("game_seats:{$event->gameUser->game->code}");
        $count = 0;

        foreach ($game as $userId) {
            if (trim($userId) !== '') {
                $count++;
            }
        }

        if ($count >= 2) {
            $event->gameUser->game->update(['started' => true]);

            // no need to lock requests, as that will be done at a seat level
            // this will be where the first 'deal' event will sit
            // use shouldQueue() here in the event of a game being in progress
        }
    }
}
