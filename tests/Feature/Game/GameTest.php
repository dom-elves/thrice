<?php

use App\Events\GameUserJoined;
use App\Events\GameUserLeft;
use App\Jobs\CloseGame;
use App\Listeners\GameUserLeftListener;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
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

test('user can create a game with a name', function () {
    $response = $this->post(route('game.create', [
        'name' => 'test name',
    ]));

    $response->assertSessionHasNoErrors()
        ->assertRedirect($response->getTargetUrl());

    $this->assertDatabaseHas('games', [
        'name' => 'test name',
    ]);
});

test('user can create a game with a password', function () {
    $response = $this->post(route('game.create', [
        'password' => 'test password',
    ]));

    $game = Game::first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect($response->getTargetUrl());

    $this->assertDatabaseHas('games', [
        'password' => $game->password,
    ]);
});

test('user can create a private game', function () {
    $response = $this->post(route('game.create', [
        'private' => 1,
    ]));

    $response->assertSessionHasNoErrors()
        ->assertRedirect($response->getTargetUrl());

    $this->assertDatabaseHas('games', [
        'private' => 1,
    ]);
});

test('user can create a game with default settings', function () {
    $response = $this->post(route('game.create'));

    $response->assertSessionHasNoErrors()
        ->assertRedirect($response->getTargetUrl());

    $this->assertDatabaseHas('games', [
        'name' => $this->user->name."'s Game",
        'password' => null,
        'private' => 0,
    ]);
});

test('user can join a created game, and has a user created for them', function () {
    Event::fake();

    $game = Game::factory()->create();

    $response = $this->get(route('game.show', $game));

    Event::assertDispatched(GameUserJoined::class);

    $response->assertInertia(fn (Assert $page) => $page->component('Game')
        ->has('game')
        ->where('game.id', $game->id)
    );

    $this->assertDatabaseHas('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
    ]);
});

test('user can join a game with no password', function () {
    Event::fake();

    $game = Game::factory()->create();

    $response = $this->get(route('game.show', $game));

    Event::assertDispatched(GameUserJoined::class);

    $response->assertInertia(fn (Assert $page) => $page->component('Game')
        ->has('game')
        ->where('game.id', $game->id)
    );
});

test('user can join a game with a password', function () {});

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

test('user can not join a game that is full', function () {
    Event::fake();
    $game = Game::factory()->create([
        'started' => true,
    ]);

    $users = User::factory()
        ->count(6)
        ->state(new Sequence(
            fn (Sequence $sequence) => [
                'name' => 'Name '.$sequence->index,
                'email' => 'email'.$sequence->index.'@example.com',
            ]
        ))
        ->create();

    foreach ($users as $user) {
        $gameUser = GameUser::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'in_game' => 1,
        ]);
    }

    $response = $this->get(route('game.show', $game));

    Event::assertNotDispatched(GameUserJoined::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game is full');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
    ]);
});

test('leaving the game via the button removes the user from the game', function () {
    Event::fake();
    $game = Game::factory()->create();
    GameUser::factory()->create([
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

test('leaving a game as the last person fires the close game job', function () {
    Queue::fake();

    $game = Game::factory()->create([
        'started' => 1,
    ]);

    $gameUser = GameUser::factory()->create([
        'user_id' => $this->user->id,
        'game_id' => $game->id,
        'in_game' => 1,
    ]);

    // because listeners are also queued, it gets in the way of the job assertion
    // so it must be faked to handle the event
    (new GameUserLeftListener)->handle(new GameUserLeft($gameUser));

    Queue::assertPushed(CloseGame::class, fn ($job) => $job->gameId == $game->id);
});

test('leaving a game that never started soft deletes the game', function () {
    $game = Game::factory()->create([
        'started' => 0,
    ]);

    Redis::hmset("game:{$game->code}", [
        'name' => $game->name,
        'hands' => 0,
        'started' => $game->started,
        'finished' => $game->finished,
    ]);

    (new CloseGame($game->id))->handle();

    $this->assertSoftDeleted('games', [
        'id' => $game->id,
    ]);
});

test('leaving a game that started does not soft delete the game', function () {
    $game = Game::factory()->create([
        'started' => 1,
    ]);

    Redis::hmset("game:{$game->code}", [
        'name' => $game->name,
        'hands' => 0,
        'started' => $game->started,
        'finished' => $game->finished,
    ]);

    (new CloseGame($game->id))->handle();

    $this->assertDatabaseHas('games', [
        'finished' => 1,
    ]);
});

// todo: fix this
// test('leaving the game via logging out removes the user from the game', function () {
//     Event::fake();
//     $game = Game::factory()->create();
//     $gameUser = GameUser::factory()->create([
//         'user_id' => $this->user->id,
//         'game_id' => $game->id,
//         'in_game' => 1,
//     ]);

//     $this->post(route('logout'));

//     Event::assertDispatched(GameUserLeft::class);

//     $this->assertDatabaseHas('game_users', [
//         'user_id' => $this->user->id,
//         'game_id' => $game->id,
//         'in_game' => 0,
//     ]);
// });

// no iea how to actually do this, must look into it
test('leaving the game via closing the active tab/window removes the user from the game', function () {});
