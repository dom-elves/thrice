<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LobbyController;
use App\Models\GameUser;
use Illuminate\Support\Facades\Route;

// dd(GameUser::all());

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
});

// lobby
Route::middleware('auth')->group( function () {
    Route::post('/lobby/create', [LobbyController::class, 'create'])->name('lobby.create');

    Route::get('/lobby/{code}', [LobbyController::class, 'show'])->name('lobby.show');
});

// game
Route::middleware('auth')->group(function () {
    Route::post('/create-game', [GameController::class, 'create'])->name('game.create');
    Route::get('/leave-game/{id}', [GameController::class, 'leave'])->name('game.leave');

    Route::post('/game/{game}/ready', [GameController::class, 'ready'])->name('game.ready');
    // this is just for testing
    // Route::post('/play-hand', [GameController::class, 'play'])->name('play.hand');
});

Route::middleware(['auth', 'game.access'])->group(function () {
    Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
});
