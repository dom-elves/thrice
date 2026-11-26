<?php

namespace Database\Seeders\Hands;

use App\Models\Game;
use App\Models\GameUser;
use Database\Seeders\Game\GameUserHandSeeder;
use Database\Seeders\Game\RowSeeder;
use Illuminate\Database\Seeder;

class StraightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $game = Game::first();

        $gameUsers = GameUser::where('game_id', $game->id)
            ->where('in_game', true)
            // should always be the same 2, but jic
            ->limit(2)
            ->get();

        $this->call(RowSeeder::class, false, // false is for silent in console or something
            [
                'game' => $game,
                'row' => 'bottom',
                'cards' => ['5h', '6h', '7h', 'Ad', 'Ac'],
            ]);

        $this->call(RowSeeder::class, false,
            [
                'game' => $game,
                'row' => 'middle',
                'cards' => ['3s', '4s', '5s', '6s', '7s'],
            ]);

        $this->call(RowSeeder::class, false,
            [
                'game' => $game,
                'row' => 'top',
                'cards' => ['8s', '9s', '8d', '7d', '6d'],
            ]);

        $this->call(GameUserHandSeeder::class, false,
            [
                'gameUser' => $gameUsers[0],
                'cards' => ['4h', '3h', '4c', '6c', '10d', '9d'],
            ]);

        $this->call(GameUserHandSeeder::class, false,
            [
                'gameUser' => $gameUsers[1],
                'cards' => ['Jc', 'Jd', 'Jh', 'Js', 'Ks', 'Kd'],
            ]);
    }
}
