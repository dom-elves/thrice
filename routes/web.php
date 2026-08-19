<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('/game', 'Game')->name('game');
});


// game
Route::middleware('auth')->group(function () {
    Route::post('/create-game', [GameController::class, 'create'])->name('game.create');
});