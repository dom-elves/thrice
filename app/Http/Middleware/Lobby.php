<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\LobbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Lobby
{
    /**
     * Handle an incoming request.
     * - check if the lobby exists
     * - check if it's full
     * - lobby is now 'joinable', so add user to redis lobby if not already there
     * - then move onto 'show' method
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->route('code');
        $key = "lobby:{$code}:user_ids";

        if (! Redis::exists($key)) {
            Inertia::flash([
                'message' => 'Lobby does not exist',
            ]);

            return redirect('dashboard');
        }

        $user = auth()->user();
        $member = Redis::sismember($key, $user->id);
        $full = Redis::scard($key) === 6;

        if (! $member && $full) {

            Inertia::flash([
                'message' => 'Lobby is full',
            ]);

            return redirect('dashboard');
        }

        if (! $member) {
            $lobbyService = app(LobbyService::class);
            $lobbyService->join($user, $code);
        }

        $request->session()->put('lobby_code', $code);

        return $next($request);
    }
}
