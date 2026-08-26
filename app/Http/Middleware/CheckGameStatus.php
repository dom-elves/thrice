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
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $gameId = (int) $request->route('id');
        $game = Game::query()->find($gameId);

        switch($game) {
            case !$game:
                $message = 'Game does not exist';
                break;
            // some annoying type error stops me using $game->finished
            case $game->finished == true:
                $message = 'Game is finished';
                break;
            // further in the future this will need to change to check redis, 
            // as game->users or even game->game_users can go beyond 6 
            // if a game last long enough
            case $game->users->count() >= 6:
                $message = 'Game is full';
                break;
            default:
                return $next($request);
        }

        if ($message) {
            Inertia::flash([
                'message' => $message,
            ]);

            return redirect('dashboard');
        }

        return $next($request);
    }
}
