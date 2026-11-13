<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\Redis;

class DeckService
{
    public function __construct() {

    }

    public function deal(Game $game): void
    {
        $seats = Redis::hgetall("game_seats:{$game->code}");

        // build deck from enums
}