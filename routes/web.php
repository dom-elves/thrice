<?php

use App\Http\Controllers\GameController;
use App\Http\Middleware\CheckGameStatus;
use App\Models\GameUser;
use Illuminate\Support\Facades\Route;

// dd(GameUser::all());

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
});

// game
Route::middleware('auth')->group(function () {
    Route::post('/create-game', [GameController::class, 'create'])->name('game.create');
    Route::get('/leave-game/{id}', [GameController::class, 'leave'])->name('game.leave');

    // this is just for testing
    Route::post('/play-hand', [GameController::class, 'play'])->name('play.hand');
});

Route::middleware(['auth', 'game.status', 'game.access'])->group(function () {
    Route::get('/game/{game}', [GameController::class, 'show'])->name('game.show');
});
