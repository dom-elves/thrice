<?php

namespace App\Http\Controllers;

use App\Services\LobbyService;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LobbyController extends Controller
{
    /**
     * Brief logic explanation:
     * - If no lobby, redirect
     * - If full & not already in, redirect
     * - If not already in, add
     * - Enter
     */
    public function show(string $code): RedirectResponse|InertiaResponse
    {
        if (! Redis::exists("lobby:{$code}:user_ids")) {
            Inertia::flash([
                'message' => 'Lobby does not exist',
            ]);

            return redirect('dashboard');
        }

        $user = auth()->user();
        $member = Redis::sismember("lobby:{$code}:user_ids", $user->id);
        $full = Redis::scard("lobby:{$code}:user_ids)") === 6;

        if ($full && !$member) {
            Inertia::flash([
                'message' => 'Lobby is full',
            ]);

            return redirect('dashboard');
        }

        if (!$member) {
            $lobbyService = app(LobbyService::class);
            // will need to possibly add user/pw details here in the future
            $data['join_code'] = $code;
            // currently using create when i could have an identical join but, we'll see
            $lobbyService->create($user, $data);
        }

        return Inertia::render('Lobby', [
            'code' => $code,
        ]);
    }

    public function create(Request $request, LobbyService $lobbyService): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        if (! isset($data['name'])) {
            $data['name'] = auth()->user()->name."'s Game";
        }

        $code = Str::lower(Str::random(12));

        $data['join_code'] = $code;

        $lobbyService->create(auth()->user(), $data);

        return redirect()->route('lobby.show', $code);
    }

    public function leave(Request $request, string $code): RedirectResponse
    {
        $lobbyService = app(LobbyService::class);

        $lobbyService->leave(auth()->user(), $code);

        return redirect()->route('dashboard');
    }
}
