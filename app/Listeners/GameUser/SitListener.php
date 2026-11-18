<?php

namespace App\Listeners\GameUser;

use App\Events\GameUser\Sit;
use App\Services\GameService;
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
     * - query is done via redis as mysql can have a history of game users
     */
    public function handle(Sit $event): void
    {
        $gameSeatIds = Redis::hgetall("game_seats:{$event->gameUser->game->code}");
        $count = 0;

        foreach ($gameSeatIds as $userId) {
            if (trim($userId) !== '') {
                $count++;
            }
        }

        if ($count >= 2) {
            app(GameService::class)->start($event->gameUser->game);

            // no need to lock requests, as that will be done at a seat level
            // this will be where the first 'deal' event will sit
            // use shouldQueue() here in the event of a game being in progress
        }
    }
}
