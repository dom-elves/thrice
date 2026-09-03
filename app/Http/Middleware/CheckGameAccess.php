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
        $game = Game::findOrFail((int) $request->route('game'));
        $userId = auth()->user()->id;
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->first();

        if (Redis::scard("game:{$game->id}:game_user_ids") === 6) {
            Inertia::flash([
                'message' => 'Game is full',
            ]);

            return redirect('dashboard');
        }

        // still need to figure out how to keep the active session
        // and not replace/break it
        $ingame = Redis::sismember("game:{$game->id}:game_user_ids", $gameUser->id);

        // todo: figure out best game is full condition
        // user has never been in game
        // user has previously been in game
        // user is in game?

        return $next($request);
    }
}
