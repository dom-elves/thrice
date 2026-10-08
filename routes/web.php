<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HandController;
use App\Http\Controllers\LobbyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
});

// lobby
// extra middleware logic only really applies to joining lobbies
// but is a bit too much for the show() method
// since that is hit via a redirect from create()
Route::middleware('auth')->group(function () {
    Route::get('/lobby/{code}', [LobbyController::class, 'show'])->name('lobby.show')
        ->middleware('lobby');
    Route::post('/lobby/create', [LobbyController::class, 'create'])->name('lobby.create');
    Route::post('/lobby/{code}/ready', [LobbyController::class, 'ready'])->name('lobby.ready');
    Route::post('/lobby/{code}/leave', [LobbyController::class, 'leave'])->name('lobby.leave');
});

// game
// follows the same pattern as lobby group
Route::middleware('auth')->group(function () {
    // sets to game:code just so it appears that way in browser, rather than /game/1 etc
    // ->missing() callback is necessary because binding comes before middleware
    Route::get('/game/{game:code}', [GameController::class, 'show'])->name('game.show')
        ->middleware('game')
        ->missing(function (Request $request) {
            Inertia::flash([
                'message' => 'Game does not exist',
            ]);

            return redirect('dashboard');
        });

    Route::get('/game/{game:code}/leave', [GameController::class, 'leave'])->name('game.leave');
});

// hand
Route::middleware('auth')->group(function () {
    Route::post('/hand/{gameUser}/ready', [HandController::class, 'ready'])->name('hand.ready');
});
