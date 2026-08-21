<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('/game', 'Game')->name('game');
});

// game
Route::middleware('auth')->group(function () {
    Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
    Route::post('/create-game', [GameController::class, 'create'])->name('game.create');
});
