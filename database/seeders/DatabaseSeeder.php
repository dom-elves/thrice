<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Redis::flushDB();

        User::factory()->create([
            'name' => 'Dom Elves',
            'email' => 'dom@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Dom2 Elves2',
            'email' => 'dom2@example.com',
            'password' => bcrypt('password'),
        ]);

        $game = Game::create([
            'name' => 'test game',
            'password' => '',
            'finished' => 0,
        ]);

        Redis::pipeline(function ($pipe) use ($game) {
            $pipe->hmset("game:{$game->id}", [
                'name' => $game->name,
                'hands' => 0,
                'finished' => $game->finished ? '1' : '0',
                'start' => $game->created_at->toDateTimeString(),
            ]);
        });

        $names = ['harry', 'ty', 'bag', 'lewis', 'remi'];

        foreach ($names as $name) {
            $user = User::create([
                'name' => $name,
                'email' => $name.'@example.com',
                'password' => bcrypt('password'),
            ]);

            $gameUser = GameUser::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
