<?php

use App\Http\Controllers\GameController;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

// auth
Route::middleware('auth')->group(function () {
    // move to controller to make optional games prop
    Route::get('/dashboard', function () {
        // todo, when testing at scale:
        // - index 'finished'
        // - paginate, do that when researching interfaces
        return Inertia::render('Dashboard', [
            'games' => Inertia::optional(fn () => Game::where('finished', 0)
                ->with([
                    'gameUsers' => fn ($query) => $query->where('in_game', 1),
                ])
                ->get()
            ),
        ]);
    })->name('dashboard');
});

// game
Route::middleware('auth')->group(function () {
    Route::post('/game/create', [GameController::class, 'create'])->name('game.create');

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
    Route::post('/game/{game:code}/sit', [GameController::class, 'sit'])->name('game.sit');
    Route::post('/game/{game:code}/stand', [GameController::class, 'stand'])->name('game.stand');
    Route::post('/game/{game:code}/leave', [GameController::class, 'leave'])->name('game.leave');
});
