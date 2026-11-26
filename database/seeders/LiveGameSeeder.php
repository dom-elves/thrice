<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Hands\HouseSeeder;
use Database\Seeders\Hands\QuadsSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class LiveGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a live (in redis) game with two in game users
     */
    public function run(): void
    {
        Redis::flushDB();

        $game = Game::factory()->create([
            'name' => 'seed game',
            'code' => 'seedgame',
            'started' => true,
        ]);

        $this->call(UserSeeder::class);

        Redis::hmset("game:{$game->code}", [
            'name' => $game->name,
            'code' => 'seedgame',
            'hands' => 0,
            'started' => $game->started,
            'finished' => $game->finished,
        ]);

        $users = User::all()->take(2);

        foreach ($users as $user) {
            GameUser::factory()->create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'in_game' => true,
            ]);
        }

        $gameUsers = GameUser::all()->take(2);

        Redis::hmset("game_seats:{$game->code}", [
            '0' => $gameUsers[0]->id,
            '1' => $gameUsers[1]->id,
            '2' => '',
            '3' => '',
            '4' => '',
            '5' => '',
        ]);

        foreach ($gameUsers as $gameUser) {
            Redis::hmset("game_user:{$gameUser->id}", [
                'game_id' => $gameUser->game->id,
                'user_id' => $gameUser->user->id,
                'name' => $gameUser->getAttribute('name'),
                'start_balance' => $gameUser->start_balance,
                'end_balance' => $gameUser->end_balance,
                'join_time' => Carbon::now()->toDateTimeString(),
                'leave_time' => '',
                'in_game' => 1,
                'hand_locked' => 0,
            ]);
        }

        $this->call(
            QuadsSeeder::class
            // HouseSeeder::class
        );
    }
}
