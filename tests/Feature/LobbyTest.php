<?php

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

test('a user can set themselves to ready', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $response = $this->followingRedirects()
        ->post(route('lobby.ready', ['code' => $joinCode]));

    // todo: assert game not started after game start is built
    $response->assertSessionHas('isReady', true);

    $response->assertInertia(fn (Assert $page) => $page->component('Lobby')
        // ->has("session.isReady.{$joinCode}")
        // ->where("session.isReady.{$joinCode}", true)
        // extra assertion for not enough players
        ->hasFlash('message', 'Not enough players ready')
    );
});

test('a user setting themselves to ready will not start the game if not all players are ready', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    for ($i = 2; $i < 5; $i++) {
        Redis::sadd("lobby:{$joinCode}:user_ids", $i);
    }

    $response = $this->followingRedirects()
        ->post(route('lobby.ready', ['code' => $joinCode]));

    $response->assertSessionHas('isReady', true);

    $response->assertInertia(fn (Assert $page) => $page->component('Lobby')
        // ->has("session.isReady.{$joinCode}")
        // ->where("session.isReady.{$joinCode}", true)
        ->hasFlash('message', 'Not all players are ready')
    );
});

test('game will start if over two users are all ready', function () {});
