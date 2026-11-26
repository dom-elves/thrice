<?php

namespace Database\Seeders\Hands;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Database\Seeders\LiveGameSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class QuadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     */
    public function run(): void
    {
        $game = Game::first();

        $gameUsers = GameUser::where('game_id', $game->id)
            ->where('in_game', true)
            ->get()
            // should always be the same 2, but jic
            ->take(2);

        Redis::hmset("game_seats:{$game->code}", [
            '0' => $gameUsers[0]->id,
            '1' => $gameUsers[1]->id,
            '2' => '',
            '3' => '',
            '4' => '',
            '5' => '',
        ]);

        Redis::hmset("bottom:{$game->code}", [
            'flop_1' => 'Ac',
            'flop_2' => 'Ad',
            'flop_3' => '6h',
            'turn' => '7h',
            'river' => '8h',
        ]);

        Redis::hmset("middle:{$game->code}", [
            'flop_1' => '2d',
            'flop_2' => '3d',
            'flop_3' => '4d',
            'turn' => '5d',
            'river' => '6d',
        ]);

        Redis::hmset("top:{$game->code}", [
            'flop_1' => '2c',
            'flop_2' => '3c',
            'flop_3' => '4c',
            'turn' => '5c',
            'river' => '6c',
        ]);

        Redis::hmset("game_user:{$gameUsers[0]->id}", [
            'bottom_card_1' => 'Ah',
            'bottom_card_2' => 'As',
            'middle_card_1' => '2h',
            'middle_card_2' => '3h',
            'top_card_1' => '4h',
            'top_card_2' => '5h',
        ]);

        Redis::hmset("game_user:{$gameUsers[1]->id}", [
            'bottom_card_1' => 'Jh',
            'bottom_card_2' => 'Jc',
            'middle_card_1' => 'Jd',
            'middle_card_2' => 'Js',
            'top_card_1' => 'Qh',
            'top_card_2' => 'Qs',
        ]);
    }
}
