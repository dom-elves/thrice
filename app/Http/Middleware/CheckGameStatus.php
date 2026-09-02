<?php

namespace App\Http\Middleware;

use App\Models\Game;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckGameStatus extends Middlware
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

        if ($game === null) {
            $message = 'Game does not exist';
        } elseif ($game->finished) {
            $message = 'Game is finished';
        } elseif ($game->users->count() >= 6) {
            // todo: check redis instead, once games can exceed 6 users
            $message = 'Game is full';
        } else {
            return $next($request);
        }

        Inertia::flash([
            'message' => $message,
        ]);

        return redirect('dashboard');
    }
}
