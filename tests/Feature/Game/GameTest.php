<?php

use App\Events\GameUserCreated;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach( function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Event::fake();
});

test('user can create a game', function() {
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
});