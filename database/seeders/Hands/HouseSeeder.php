<?php

namespace Database\Seeders\Hands;

use App\Models\Game;
use App\Models\GameUser;
use Database\Seeders\Game\GameUserHandSeeder;
use Database\Seeders\Game\RowSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class HouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Can be used as TwoPair seeder by changing one of $gameUser[0]s aces
     * Also can be used as pair seeder by also changing one of the board's sixes
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
                'cards' => ['Ac', '6c', '6h', '7h', '8h'],
            ]);

         $this->call(RowSeeder::class, false,
            [
                'game' => $game, 
                'row' => 'middle', 
                'cards' => ['2d', '3d', '4d', '5d', '6d'],
            ]);
            
        $this->call(RowSeeder::class, false,
            [
                'game' => $game, 
                'row' => 'top', 
                'cards' => ['2c', '3c', '4c', '5c', '6c'],
            ]);

        $this->call(GameUserHandSeeder::class, false,
            [
                'gameUser' => $gameUsers[0],
                'cards' => ['Ah', 'As', '2h', '3h', '4h', '5h'],
            ]);

        $this->call(GameUserHandSeeder::class, false,
            [
                'gameUser' => $gameUsers[1],
                'cards' => ['Jh', 'Jc', 'Jd', 'Js', 'Qh', 'Qs'],
            ]);
    }
}
