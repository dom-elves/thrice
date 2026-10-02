<?php

namespace App\Http\Middleware;

use App\Models\GameUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Game
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $game = $request->game;

        if (! $game->started) {
            Inertia::flash([
                'message' => 'Game has not started yet',
            ]);

            return redirect('dashboard');
        }

        if ($game->finished) {
            Inertia::flash([
                'message' => 'Game has finished',
            ]);

            return redirect('dashboard');
        }
        
        // with this, if a user closes the tab/window and tries to reopen it
        // they will not be let back in, but i plan on putting a ttl on redis users
        // so they will be automatically kicked if they do not set cards
        $full = $game->gameUsers->where('in_game', true)->count() === 6;

        if ($full) {
            Inertia::flash([
                'message' => 'Game is full',
            ]);

            return redirect('dashboard');
        }

        // as the game is now 'joinable', we can get the user in the game
        // this lives here & not the controller method, as users are directed to show()
        // after game creation, in which users are also created
        //... or does it make more sense to have creation logic in show?

        $request->session()->put('game_code', $game->code);

        return $next($request);
    }
}
