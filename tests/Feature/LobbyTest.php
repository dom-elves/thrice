<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Dom Elves',
        'email' => 'dom@example.com',
    ]);

    $this->users = User::factory()->count(5)->create();
    $this->actingAs($this->user);
    Event::fake();
});

test('a user can create a lobby', function () {
    // game name and pw are optional, will be tested in game controller
    $response = $this->post(route('lobby.create'));

    $response->assertSessionHasNoErrors()
        ->assertRedirect($response->getTargetUrl());
});

test('a user can join a lobby', function () {
    // as this is all redis, gotta just create on again
    // then join as a different user
    // todo: page assertions when frontend is built, page contains etc

    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $response = $this->actingAs($this->users[0])
        ->get(route('lobby.show', [
            'code' => $joinCode,
        ]));

    $response->assertSessionHasNoErrors();
});

test('a user can not join a lobby that does not exist', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $response = $this->actingAs($this->users[0])
        ->get(route('lobby.show', [
            'code' => $joinCode.'-not-a-real-code',
        ]));

    $response->assertRedirect('dashboard')
        ->assertInertiaFlash('message', 'Lobby does not exist');
});
