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
        // todo: this causes 404 where find() causes null
        // so eventually, build a proper 404 page
        $game = Game::findOrFail((int) $request->route('id'));

        if ($game->finished) {
            Inertia::flash([
                'message' => 'Game is finished',
            ]);

            return redirect('dashboard');
        }

        return $next($request);
    }
}
