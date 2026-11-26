<?php

namespace Database\Seeders\Game;

use App\Models\GameUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class GameUserHandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param  GameUser  $gameUser
     * @param  array<int, string>  $cards
     */
    public function run($gameUser, $cards): void
    {
        Redis::hmset("game_user:{$gameUser->id}", [
            'bottom_card_1' => $cards[0],
            'bottom_card_2' => $cards[1],
            'middle_card_1' => $cards[2],
            'middle_card_2' => $cards[3],
            'top_card_1' => $cards[4],
            'top_card_2' => $cards[5],
        ]);
    }
}
