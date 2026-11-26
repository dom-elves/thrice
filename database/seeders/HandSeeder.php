<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use App\Services\DeckService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;

class HandSeeder extends Seeder
{
    public function __construct()
    {
        $this->deck = app(DeckService::class)->build();
    }

    /**
     * Run the database seeds. B
     */
    public function run(): void
    {
        Redis::flushDB();

        $game = Game::factory()->create([
            'name' => 'quads game',
            'code' => 'quadsgame',
            'started' => true,
        ]);

        $names = ['Dom', 'Harry', 'Ty', 'Bag', 'Lewis', 'Remi'];

        foreach ($names as $name) {
            User::factory()->create([
                'name' => $name,
                'email' => $name.'@example.com',
            ]);
        }

        // $this->twoPair($game);

        $this->highCard($game);
    }

    // did change this to pair, oops
    private function twoPair($game): void
    {
        $users = User::all()->take(2);

        foreach ($users as $user) {
            GameUser::factory()->create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'in_game' => true,
            ]);
        }

        $gameUsers = GameUser::all();
        // dd($gameUsers);
        Redis::hmset("game_user:{$gameUsers[0]->id}", [
            'game_id' => $gameUsers[0]->game->id,
            'user_id' => $gameUsers[0]->user->id,
            'name' => $gameUsers[0]->getAttribute('name'),
            'start_balance' => $gameUsers[0]->start_balance,
            'end_balance' => $gameUsers[0]->end_balance,
            'join_time' => Carbon::now()->toDateTimeString(),
            'leave_time' => '',
            'in_game' => 1,
            'hand_locked' => 0,
            'bottom_card_1' => 'Ah',
            'bottom_card_2' => 'As',
            'middle_card_1' => '2h',
            'middle_card_2' => '3h',
            'top_card_1' => '4h',
            'top_card_2' => '5h',
        ]);

        Redis::hmset("game_user:{$gameUsers[1]->id}", [
            'game_id' => $gameUsers[1]->game->id,
            'user_id' => $gameUsers[1]->user->id,
            'name' => $gameUsers[1]->getAttribute('name'),
            'start_balance' => $gameUsers[1]->start_balance,
            'end_balance' => $gameUsers[1]->end_balance,
            'join_time' => Carbon::now()->toDateTimeString(),
            'leave_time' => '',
            'in_game' => 1,
            'hand_locked' => 0,
            'bottom_card_1' => 'Jh',
            'bottom_card_2' => 'Jc',
            'middle_card_1' => 'Jd',
            'middle_card_2' => 'Js',
            'top_card_1' => 'Qh',
            'top_card_2' => 'Qs',
        ]);

        Redis::hmset("game_seats:{$game->code}", [
            '0' => $gameUsers[0]->id,
            '1' => $gameUsers[1]->id,
            '2' => '',
            '3' => '',
            '4' => '',
            '5' => '',
        ]);

        Redis::hmset("bottom:{$game->code}", [
            'flop_1' => '9d',
            'flop_2' => '4s',
            'flop_3' => 'Kd',
            'turn' => 'jh',
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
    }

    private function highCard($game): void
    {
        $users = User::all()->take(2);

        foreach ($users as $user) {
            GameUser::factory()->create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'in_game' => true,
            ]);
        }

        $gameUsers = GameUser::all();
        // dd($gameUsers);
        Redis::hmset("game_user:{$gameUsers[0]->id}", [
            'game_id' => $gameUsers[0]->game->id,
            'user_id' => $gameUsers[0]->user->id,
            'name' => $gameUsers[0]->getAttribute('name'),
            'start_balance' => $gameUsers[0]->start_balance,
            'end_balance' => $gameUsers[0]->end_balance,
            'join_time' => Carbon::now()->toDateTimeString(),
            'leave_time' => '',
            'in_game' => 1,
            'hand_locked' => 0,
            'bottom_card_1' => 'Ah',
            'bottom_card_2' => '2s',
            'middle_card_1' => '2h',
            'middle_card_2' => '3h',
            'top_card_1' => '4h',
            'top_card_2' => '5h',
        ]);

        Redis::hmset("game_user:{$gameUsers[1]->id}", [
            'game_id' => $gameUsers[1]->game->id,
            'user_id' => $gameUsers[1]->user->id,
            'name' => $gameUsers[1]->getAttribute('name'),
            'start_balance' => $gameUsers[1]->start_balance,
            'end_balance' => $gameUsers[1]->end_balance,
            'join_time' => Carbon::now()->toDateTimeString(),
            'leave_time' => '',
            'in_game' => 1,
            'hand_locked' => 0,
            'bottom_card_1' => 'Jh',
            'bottom_card_2' => 'Jc',
            'middle_card_1' => 'Jd',
            'middle_card_2' => 'Js',
            'top_card_1' => 'Qh',
            'top_card_2' => 'Qs',
        ]);

        Redis::hmset("game_seats:{$game->code}", [
            '0' => $gameUsers[0]->id,
            '1' => $gameUsers[1]->id,
            '2' => '',
            '3' => '',
            '4' => '',
            '5' => '',
        ]);

        Redis::hmset("bottom:{$game->code}", [
            'flop_1' => '9d',
            'flop_2' => '4s',
            'flop_3' => 'Kd',
            'turn' => 'jh',
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
    }
}
