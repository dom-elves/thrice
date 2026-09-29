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

        if (! Redis::exists("lobby:{$code}:user_ids")) {
            Inertia::flash([
                'message' => 'Lobby does not exist',
            ]);

            return redirect('dashboard');
        }

        $user = auth()->user();
        $member = Redis::sismember("lobby:{$code}:user_ids", $user->id);
        $full = Redis::scard("lobby:{$code}:user_ids)") === 6;

        if ($full && ! $member) {
            Inertia::flash([
                'message' => 'Lobby is full',
            ]);

            return redirect('dashboard');
        }

        if (! $member) {
            $lobbyService = app(LobbyService::class);
            $obbyService->join($user, $code);
        }

        $request->session()->put('lobby_code', $code);

        return $next($request);
    }
}
