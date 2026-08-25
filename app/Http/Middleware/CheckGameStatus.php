<?php

namespace App\Http\Middleware;

use App\Models\Game;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckGameStatus
{
    /**
     * Handle an incoming request.
     * todo: refactor this, just need it working for now
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $gameId = (int) $request->route('id');
        $game = Game::query()->find($gameId);

        // game does not exist
        if (! $game) {
            Inertia::flash([
                'message' => 'Game does not exist',
            ]);

            return redirect('dashboard');
        }

        // game is finished
        if ($game->finished) {
            Inertia::flash([
                'message' => 'Game is finished',
            ]);

            return redirect('dashboard');
        }

        // game is full
        if ($game->users->count() >= 6) {
            Inertia::flash([
                'message' => 'Game is full',
            ]);

            return redirect('dashboard');
        }

        return $next($request);
    }
}
