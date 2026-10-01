<?php

use App\Events\GameUserJoined;
use App\Events\GameUserLeft;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Dom Elves',
        'email' => 'dom@example.com',
    ]);

    $this->users = User::factory()->count(5)->create();
    $this->actingAs($this->user);
});


// test('user can join a created game, and has a user created for them', function () {
//     $game = Game::factory()->create();
//     $user = User::factory()->create();

//     $response = $this->actingAs($user)->get(route('game.show', $game));

//     Event::assertDispatched(GameUserJoined::class);

//     $response->assertInertia(fn (Assert $page) => $page->component('Game')
//         ->has('game')
//         ->where('game.id', $game->id)
//     );

//     $this->assertDatabaseHas('games', [
//         'name' => $game->name,
//         'password' => $game->password,
//     ]);

//     $this->assertDatabaseHas('game_users', [
//         'user_id' => $user->id,
//         'game_id' => $game->id,
//     ]);
// });

// test('user can join a game with no password', function () {
//     $game = Game::factory()->create();
//     $user = User::factory()->create();

//     $response = $this->actingAs($user)->get(route('game.show', $game));

//     Event::assertDispatched(GameUserJoined::class);

//     $response->assertInertia(fn (Assert $page) => $page->component('Game')
//         ->has('game')
//         ->where('game.id', $game->id)
//     );
// });

// test('user can join a game with a password', function () {});

test('user can not join a game that does not exist', function () {
    Event::fake();
    $response = $this->get(route('game.show', ['game' => 'blalblabl']));

    Event::assertNotDispatched(GameUserJoined::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game does not exist');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $this->user->id,
    ]);
});

test('user can not join a game that has not started', function () {
    Event::fake();
    $game = Game::factory()->create([
        'started' => 0,
    ]);

    $response = $this->get(route('game.show', $game));

    Event::assertNotDispatched(GameUserJoined::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game has not started yet');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
    ]);
});

test('user can not join a game that has finished', function () {
    Event::fake();
    
    $game = Game::factory()->create([
        'started' => 1,
        'finished' => 1,
    ]);

    $response = $this->get(route('game.show', $game));

    Event::assertNotDispatched(GameUserJoined::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game has finished');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
    ]);
});

// commenting out this test for now as I may treat game capacity differently
// depending on if I decide to had a db column for game like "max_players" or "is_full"

// test('user can not join a game that is full' , function () {
//     $game = Game::factory()->create();
//     $users = User::all();

//     $extra_user = User::factory()->create([
//         'name' => 'Do not let me join',
//         'email' => 'donotletmejoin@example.com',
//     ]);

//     foreach ($users as $user) {
//         $gameUser =  GameUser::factory()->create([
//             'user_id' => $user->id,
//             'game_id' => $game->id,
//             'in_game' => 1,
//         ]);

//         Redis::pipeline(function ($pipe) use ($gameUser) {
//             $pipe->hgetdel("game_user:{$gameUser->id}", [
//                 'game_id',
//                 'user_id',
//                 'start_balance',
//                 'end_balance',
//                 'join_time',
//                 'leave_time',
//                 'in_game',
//                 'user_session_id',
//             ]);
//         });
//     }

//     $response = $this->actingAs($extra_user)->get(route('game.show', $game));

//     Event::assertNotDispatched(GameUserJoined::class);

//     $response->assertRedirect('dashboard')
//         ->assertInertiaFlash('message', 'Game is full');

//     $this->assertDatabaseMissing('game_users', [
//         'user_id' => $extra_user->id,
//         'game_id' => $game->id,
//     ]);
// });

test('leaving the game via the button removes the user from the game', function () {
    Event::fake();
    $game = Game::factory()->create();
    $gameUser = GameUser::factory()->create([
        'user_id' => $this->user->id,
        'game_id' => $game->id,
        'in_game' => 1,
    ]);

    $this->get(route('game.leave', $game->code));

    Event::assertDispatched(GameUserLeft::class);

    $this->assertDatabaseHas('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
        'in_game' => 0,
    ]);
});

test('leaving the game via logging out removes the user from the game', function () {
    Event::fake();
    $game = Game::factory()->create();
    $gameUser = GameUser::factory()->create([
        'user_id' => $this->user->id,
        'game_id' => $game->id,
        'in_game' => 1,
    ]);

    $this->post(route('logout'));

    Event::assertDispatched(GameUserLeft::class);

    $this->assertDatabaseHas('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
        'in_game' => 0,
    ]);
});

// no iea how to actually do this, must look into it
test('leaving the game via closing the active tab/window removes the user from the game', function () {});

// todo: tests for invites etc, when that is built
