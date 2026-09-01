<?php

use App\Events\GameUserCreated;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Dom Elves',
        'email' => 'dom@example.com',
    ]);

    $this->users = User::factory()->count(5)->create();
    $this->actingAs($this->user);
    Event::fake();
});

test('user can create a game, and a game user for them is created', function () {
    $response = $this->post(route('game.create'), [
        'name' => 'test game',
        'password' => 'password',
    ]);

    Event::assertDispatched(GameUserCreated::class);

    $game = Game::where('name', 'test game')->first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect("game/{$game->id}");

    $this->assertDatabaseHas('games', [
        'name' => $game->name,
        'password' => $game->password,
    ]);

    $this->assertDatabaseHas('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
    ]);
});

test('creating a game with no name defaults it to the users name', function () {
    $response = $this->post(route('game.create'));

    Event::assertDispatched(GameUserCreated::class);

    $game = Game::first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect("game/{$game->id}");

    $this->assertDatabaseHas('games', [
        'name' => $this->user->name."'s Game",
        'password' => $game->password,
    ]);
});

test('user can join a created game, and has a user created for them', function () {
    $game = Game::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('game.show', $game));

    Event::assertDispatched(GameUserCreated::class);

    $response->assertInertia(fn (Assert $page) => $page->component('Game')
        ->has('game')
        ->where('game.id', $game->id)
    );

    $this->assertDatabaseHas('games', [
        'name' => $game->name,
        'password' => $game->password,
    ]);

    $this->assertDatabaseHas('game_users', [
        'user_id' => $user->id,
        'game_id' => $game->id,
    ]);
});

test('user can join a game with no password', function () {
    $game = Game::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('game.show', $game));

    Event::assertDispatched(GameUserCreated::class);

    $response->assertInertia(fn (Assert $page) => $page->component('Game')
        ->has('game')
        ->where('game.id', $game->id)
    );
});

test('user can join a game with a password', function () {});

test('user can not join a game that does not exist', function () {
    $response = $this->get(route('game.show', ['id' => 1000]));

    Event::assertNotDispatched(GameUserCreated::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game does not exist');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $this->user->id,
    ]);
});

test('user can not join a game that has finished', function () {
    $game = Game::factory()->create([
        'finished' => 1,
    ]);

    $response = $this->get(route('game.show', $game));

    Event::assertNotDispatched(GameUserCreated::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game is finished');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $this->user->id,
        'game_id' => $game->id,
    ]);
});

test('user can not join a game that is full', function () {
    $game = Game::factory()->create();
    $users = User::all();

    $extra_user = User::factory()->create([
        'name' => 'Do not let me join',
        'email' => 'donotletmejoin@example.com',
    ]);

    foreach ($users as $user) {
        GameUser::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
    }

    $response = $this->actingAs($extra_user)->get(route('game.show', $game));

    Event::assertNotDispatched(GameUserCreated::class);

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Game is full');

    $this->assertDatabaseMissing('game_users', [
        'user_id' => $extra_user->id,
        'game_id' => $game->id,
    ]);
});

test('leaving the game via the button removes the user from the game', function () {});

// no iea how to actually do this, must look into it
test('leaving the game via closing the active tab/window removes the user from the game', function () {});

// todo: tests for invites etc, when that is built
