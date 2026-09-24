<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LobbyController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
});

// lobby
Route::middleware('auth')->group(function () {
    Route::post('/lobby/create', [LobbyController::class, 'create'])->name('lobby.create');
    Route::get('/lobby/{code}', [LobbyController::class, 'show'])->name('lobby.show');
    Route::post('/lobby/{code}/ready', [LobbyController::class, 'ready'])->name('lobby.ready');
    Route::post('/lobby/{code}/leave', [LobbyController::class, 'leave'])->name('lobby.leave');
});

// game
Route::middleware('auth')->group(function () {
    Route::post('/create-game', [GameController::class, 'create'])->name('game.create');
    Route::get('/leave-game/{id}', [GameController::class, 'leave'])->name('game.leave');

    Route::post('/game/{game}/ready', [GameController::class, 'ready'])->name('game.ready');
    // this is just for testing
    // Route::post('/play-hand', [GameController::class, 'play'])->name('play.hand');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/game/{game}', [GameController::class, 'show'])->name('game.show');
});
