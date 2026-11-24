<?php

use App\Events\Game\Deal;
use App\Events\Game\DealBoard;
use App\Events\GameUser\Hand;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

beforeEach(function () {
    Redis::flushDB();
    
    $this->user = User::factory()->create([
        'name' => 'Dom Elves',
        'email' => 'dom@example.com',
    ]);

    $this->users = User::factory()->count(5)->create();

    $this->actingAs($this->user);
});

/**
 * Fake hand so each user hand can be asserted after deal
 */
test('starting a game deals hands to seated players', function () {
    Event::fake([Hand::class]); 
    
    $game = Game::factory()->create();
    $gameUsers = $this->users->map(fn ($user) => GameUser::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'in_game' => true,
    ]));

    foreach ($gameUsers as $key => $gameUser) {
        Redis::hmset("game_seats:{$game->code}", [
            $key => $gameUser->id,
        ]);
    }

    event(new Deal($game));

    foreach ($gameUsers as $gameUser) {
        // normalise card data formation
        $cards = Redis::hgetall("game_user:{$gameUser->id}");
        $cards = array_values($cards);

        Event::assertDispatched(Hand::class, function (Hand $event) use ($gameUser, $cards) {
            return $event->gameUserId == $gameUser->id
                && $event->hand == $cards;
        });
    }
});

/**
 * Fake only DealBoard, so it can be asserted after actually firing Deal
 */
test('starting a game deals the board', function () {
    Event::fake([DealBoard::class]); 

    $game = Game::factory()->create();
    $this->users->map(fn ($user) => GameUser::factory()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'in_game' => true,
    ]));

    event(new Deal($game));

    Event::assertDispatched(DealBoard::class);

    $this->assertEquals(true, Redis::exists("bottom:{$game->code}"));
    $this->assertEquals(true, Redis::exists("middle:{$game->code}"));
    $this->assertEquals(true, Redis::exists("top:{$game->code}"));
});

/** 
 * Inverse of firstd test, just create a gameUser that has been in the game before,
 * but isn't now
 */
test('starting a game does not deal hands to non-seated players', function () {
    Event::fake([Hand::class]); 
    
    $game = Game::factory()->create();
    $gameUser = GameUser::factory()->create([
        'user_id' => $this->users[1]->id,
        'game_id' => $game->id,
        'in_game' => false,
    ]);

    event(new Deal($game));

    Event::assertNotDispatched(Hand::class, function (Hand $event) use ($gameUser) {
            return $event->gameUserId == $gameUser->id;
        });
});