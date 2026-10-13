<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $game = Game::factory()->create([
            'name' => $user->name."'s Game",
            'private' => 0,
            'started' => 0,
            'finished' => 0,
        ]);

        GameUser::factory()
            ->for($user)
            ->create([
                'game_id' => 1,
                'in_game' => 1,
            ]);

        // add redis data after confirming how it'll work
    }
}
