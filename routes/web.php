<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckGameStatus;

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
});

// game
Route::middleware(['auth', CheckGameStatus::class])->group(function () {
    Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
    Route::post('/create-game', [GameController::class, 'create'])->name('game.create');

    // this is just for testing
    Route::post('/play-hand', [GameController::class, 'play'])->name('play.hand');
});
