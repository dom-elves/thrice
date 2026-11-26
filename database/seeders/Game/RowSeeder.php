<?php

namespace Database\Seeders\Game;

use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class RowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Bit more laborious, but keeping this atomic in case I ever need to seed single rows
     *
     * @param  array<int, string>  $cards
     */
    public function run(Game $game, string $row, array $cards): void
    {
        Redis::hmset("$row:{$game->code}", [
            'flop_1' => $cards[0],
            'flop_2' => $cards[1],
            'flop_3' => $cards[2],
            'turn' => $cards[3],
            'river' => $cards[4],
        ]);
    }
}
