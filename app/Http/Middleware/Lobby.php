<?php

namespace App\Http\Middleware;

use App\Services\LobbyService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Lobby
{
    /**
     * Handle an incoming request.
     * This middleware only gets called on the show() method,
     * and exists to keep the show() method slim.
     * 
     * - check if the lobby exists
     * - check if it's full
     * 
     * The lobby is now considered joinable
     * - if the user is already in a different lobby, pull them out
     * - then, join the lobby in question
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

        $lobbyService = app(LobbyService::class);

        if ($request->session()->get('lobby_code') !== $code) {
            $lobbyService->leave($user, $request->session()->get('lobby_code'));
        }

        if (! $member) {
            $lobbyService->join($user, $code);
        }

        $request->session()->put('lobby_code', $code);

        return $next($request);
    }
}
