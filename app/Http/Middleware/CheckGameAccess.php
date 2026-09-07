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
                'message' => 'Game does not exist',
            ]);

            return redirect('dashboard');
        }

        if ($request->session()->pull('new_game') === $game->id) {
            return $next($request);
        }

        if ($game->finished) {
            Inertia::flash([
                'message' => 'Game is finished',
            ]);

            return redirect('dashboard');
        }

        if (Redis::scard("game:{$game->id}:game_user_ids") === 6) {
            Inertia::flash([
                'message' => 'Game is full',
            ]);

            return redirect('dashboard');
        }

        /**
         * At this point, we've checked the following:
         * - game existence
         * - if game is new (just been made bu the user)
         * - if game is finished
         * - if game is full
         * 
         * From here, the game is considered to be in a "joinable" state
         * Which just means it is active, and not full
         */

        $userId = auth()->user()->id;
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->first();

        // this has to exist because a null varaible in the redis::hget for active ssession
        // will then mess with the comparison further down the line
        // so just throw the user into the controller, even if that means 1 extra query
        // otherwise, i'd have to move the session id check into the controller
        // which doesn't really make sense, as when you get to the show() method,
        // should mean the game is fully joinable
        // unless further down the line, polling prevents this check from
        // even being necesary
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
