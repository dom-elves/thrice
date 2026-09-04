<?php

namespace App\Http\Middleware;

use App\Models\Game;
use App\Models\GameUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckGameAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $game = Game::find((int) $request->route('id'));

        if (! $game) {
            Inertia::flash([
                'message' => "Game does not exist",
            ]);

            return redirect('dashboard');
        }

        if ($request->session()->pull('new_game') === $game->id) {
            return $next($request);
        }

        if ($game->finished) {
            Inertia::flash([
                'message' => "Game is finished",
            ]);

            return redirect('dashboard');
        }

        if (Redis::scard("game:{$game->id}:game_user_ids") === 6) {
            Inertia::flash([
                'message' => "Game is full",
            ]);

            return redirect('dashboard');
        }

        $userId = auth()->user()->id;
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->first();

        if (! $gameUser) {
            return $next($request);
        }

        $activeSessionId = Redis::hget("game_user:{$gameUser->id}", 'user_session_id');

        if ($activeSessionId && $activeSessionId !== session()->getId()) {
            Inertia::flash([
                'message' => "You're already in this game in another browser",
            ]);

            return redirect('dashboard');
        }

        return $next($request);
    }
}
