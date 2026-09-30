<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $code = $request->route('code');

        // this will be different as moving to json
        $key = "game:{$code}:user_ids";

        if (! Redis::exists($key)) {
            Inertia::flash([
                'message' => 'Lobby does not exist',
            ]);

            return redirect('dashboard');
        }
        // $user = auth()->user();
        // $gameUser = GameUser::where('game_id', $game->id)
        //     ->where('user_id', $user->id)
        //     ->first();

        // // todo: maybe move all this to be after a game password check
        // $gameService = app(GameService::class);

        // if (! $gameUser) {
        //     $createGameUserAction = new CreateGameUserAction($gameService);
        //     $createGameUserAction->execute($game->id, $user->id);
        // } elseif (! $gameUser->in_game) {
        //     $gameService->join($gameUser);
        // }

        return $next($request);
    }
}
