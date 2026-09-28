<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Lobby
{
    /**
     * Handle an incoming request.
     * Check if the lobby exists,
     * then check if it is full.
     * If neither, lobby is considered 'joinable'.
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

        return $next($request);
    }
}
