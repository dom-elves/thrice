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

    // this is just for testing
    Route::post('/play-hand', [GameController::class, 'play'])->name('play.hand');
});

Route::middleware(CheckGameStatus::class)->group(function () {
    Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
});
