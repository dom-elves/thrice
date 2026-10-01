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
        // todo: do  not do game full logic until figured out turns, ttl etc
        // especially not until figured out leaving on tab/window close
        // though i think it makes sense that that can hold your spot
        // and ttl just does everything else
        // $user = auth()->user();
        // $gameUser = GameUser::where('game_id', $game->id)
        //     ->where('user_id', $user->id)
        //     ->first();

        // if ($full) {
        //     Inertia::flash([
        //         'message' => 'Game is full',
        //     ]);

        //     return redirect('dashboard');
        // }

        // if (! $gameUser) {
        //     // join game
        // }

        // if (! $gameUser->in_game) {

        // }
        // follow same pattern as lobby, check game full status & if user is member via redis
        // also need to return if game not started
        // $user = auth()->user();
        // $gameUser = GameUser::where('game_id', $game->id)
        //     ->where('user_id', $user->id)
        //     ->first();

        // $gameService = app(GameService::class);

        // if (! $gameUser) {
        //     $createGameUserAction = new CreateGameUserAction($gameService);
        //     $createGameUserAction->execute($game->id, $user->id);
        // } elseif (! $gameUser->in_game) {
        //     $gameService->join($gameUser);
        // }

        $request->session()->put('game_code', $game->code);

        return $next($request);
    }
}
