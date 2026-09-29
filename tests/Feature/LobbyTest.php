<?php

use App\Events\GameCreated;
use App\Events\Lobby\UserToggleReady;
use App\Models\Game;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Dom Elves',
        'email' => 'dom@example.com',
    ]);

    $this->users = User::factory()->count(5)->create();
    $this->actingAs($this->user);
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

test('a user can set themselves to ready from not ready', function () {
    Queue::fake();
    // turns out general Event::fake() breaks Queue::fake()
    // so only fake specific event, to make sure it isn't dispatched
    Event::fake([GameCreated::class]);

    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $status = true;

    $response = $this->post(route('lobby.ready', [
            'code' => $joinCode,
            'status' => $status,
        ]));

    $response->assertSessionHasNoErrors();

    Event::assertNotDispatched(GameCreated::class);

    Queue::assertPushed(BroadcastEvent::class, function ($job) use ($status) {
        return $job->event instanceof UserToggleReady
            && $job->event->status == $status;
    });

    $response->assertRedirectBack(); 
});

test('a user can set themselves to not ready from ready', function () {
    Queue::fake();
    Event::fake([GameCreated::class]);

    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    $status = false;

    $response = $this->post(route('lobby.ready', [
            'code' => $joinCode,
            'status' => $status,
        ]));

    $response->assertSessionHasNoErrors();

    Event::assertNotDispatched(GameCreated::class);

    Queue::assertPushed(BroadcastEvent::class, function ($job) use ($status) {
        return $job->event instanceof UserToggleReady
            && $job->event->status == $status;
    });

    $response->assertRedirectBack(); 
});

test('a user setting themselves to ready will not start the game if not all players are ready', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    for ($i = 2; $i < 5; $i++) {
        Redis::sadd("lobby:{$joinCode}:user_ids", $i);
    }

    $response = $this->followingRedirects()
        ->post(route('lobby.ready', ['code' => $joinCode]));


    $response->assertInertia(fn (Assert $page) => $page->component('Lobby')
        ->hasFlash('message', 'Not all players are ready')
    );
});

test('game will start if over two users are all ready', function () {
    $response = $this->post(route('lobby.create'));
    $joinCode = basename($response->getTargetUrl());

    foreach ($this->users as $user) {
        Redis::sadd("lobby:{$joinCode}:user_ids", $user->id);
        Redis::sadd("lobby:{$joinCode}:ready_user_ids", $user->id);
    }

    $response = $this->followingRedirects()
        ->post(route('lobby.ready', ['code' => $joinCode]));

    Event::assertDispatched(GameCreated::class);

    $this->assertDatabaseHas('games', [
        'name' => $this->user->name."'s Game",
        'code' => $joinCode,
    ]);

    // i think i can only test the redirect signal in dusk or something
    // anything on the other side of the redirect will be tested in games test
});
