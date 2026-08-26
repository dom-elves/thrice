<?php

use App\Events\GameUserCreated;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach( function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Event::fake();
});

test('user can create a game, and a game user for them is created', function() {
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

test('user can join a created game, and has a user created for them', function() {
    $game = Game::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('game.show', $game));

    Event::assertDispatched(GameUserCreated::class);

    $response->assertInertia(fn (Assert $page) =>
        $page->component('Game')
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

test('user can not join a game that does not exist', function() {

});

test('user can not join a game that has finished', function() {

});

test('user can not join a game that is full', function() {

});

// todo: tests for invites etc, when that is built
// as well as looking into how to test redis