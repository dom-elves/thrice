<?php

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

test('a user can create a lobby', function () {
    $response = $this->post(route('lobby.create'));

    $response->assertSessionHasNoErrors()
        ->assertRedirect($response->getTargetUrl());
});

test('a user can join a lobby', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $response = $this->actingAs($this->users[0])
        ->get(route('lobby.show', [
            'code' => $joinCode,
        ]));

    $response->assertSessionHasNoErrors();

    $response->assertInertia(fn (Assert $page) => $page->component('Lobby')
        ->has('code')
        ->where('code', $joinCode)
    );
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

test('a user can leave a lobby', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $this->actingAs($this->users[0])
        ->get(route('lobby.show', [
            'code' => $joinCode,
        ]));

    $response = $this->actingAs($this->users[0])
        ->post(route('lobby.leave', [
            'code' => $joinCode,
        ]));

    $response->assertRedirect('dashboard');
});
