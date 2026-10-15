<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        Game::factory()->create([
            'name' => $user->name."'s Game",
            'private' => 0,
            'started' => 0,
            'finished' => 0,
        ]);

    

        Game::factory()->create([
            'name' => $user->name."'s Game but private",
            'private' => 1,
            'started' => 0,
            'finished' => 0,
        ]);

        Game::factory()->create([
            'name' => $user->name."'s Game but it's started",
            'private' => 0,
            'started' => 1,
            'finished' => 0,
        ]);

        Game::factory()->create([
            'name' => $user->name."'s Game but it's finished",
            'private' => 0,
            'started' => 0,
            'finished' => 1,
        ]);

        foreach (Game::all() as $game) {
            GameUser::factory()
                ->for($user)
                ->create([
                    'game_id' => $game->id,
                    'in_game' => $game->finished ? 0 : 1,
                ]);
        }

        // add redis data after confirming how it'll work
    }
}
