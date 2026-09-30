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
    Route::get('/game/{game}', [GameController::class, 'show'])->name('game.show')
        ->middleware('game');
    Route::get('/game/{game}/leave', [GameController::class, 'leave'])->name('game.leave');
});

