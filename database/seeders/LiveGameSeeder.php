<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class LiveGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Can't figure out a way to also have my session be the same as a seeded one
     * So the run is like this:
     * - flush redis db, as this will always run off the back of migrate:fresh --seed
     * - create user (mysql)
     * - loop over other names, creating user, gameUser, redis gameUser and redis set
     *
     * The end result is a game with 5/6 people, leaving space for myself to join
     */
    public function run(): void
    {
        // flush redis db
        Redis::flushdb();

        // game
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

        // user
        $user = User::create([
            'name' => 'dom',
            'email' => 'dom@example.com',
            'password' => bcrypt('password'),
        ]);

        // same process, but everyone else too
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

            Redis::pipeline(function ($pipe) use ($gameUser) {
                $pipe->hmset("game_user:{$gameUser->id}", [
                    'game_id' => $gameUser->game->id,
                    'user_id' => $gameUser->user->id,
                    'start_balance' => $gameUser->start_balance,
                    'end_balance' => $gameUser->end_balance,
                    'join_time' => Carbon::now()->toDateTimeString(),
                    'leave_time' => '',
                    'in_game' => 1,
                    'user_session_id' => session()->getId(),
                ]);
            });

            Redis::sadd("game:{$gameUser->game->id}:game_user_ids", $gameUser->id);
        }
    }
}
