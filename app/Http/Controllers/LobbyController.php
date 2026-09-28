<?php

namespace App\Http\Controllers;

use App\Events\Lobby\UserToggleReady;
use App\Services\LobbyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LobbyController extends Controller
{
    public function __construct(
        private LobbyService $lobbyService
    ) {}

    /**
     * - check existence of lobby
     * - check lobby is full & user is not in
     * - so if lobby is not full and user is not member, join
     */
    public function show(Request $request): RedirectResponse|InertiaResponse
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
            $this->lobbyService->join($user, $code);
        }

        $request->session()->put('lobby_code', $code);

        return Inertia::render('Lobby', [
            'code' => $code,
        ]);
    }

    /**
     * - validate data
     * - set a name if one isn't given
     * - set empty string as password if one isn't given
     * - generate lobby code & append to $data
     * - create lobby
     */
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        if (! isset($validated['name'])) {
            $validated['name'] = auth()->user()->name."'s Game";
        }

        if (! isset($validated['password'])) {
            $validated['password'] = '';
        }

        $code = Str::lower(Str::random(12));

        $validated['code'] = $code;

        $this->lobbyService->create(auth()->user(), $validated);

        $request->session()->put('lobby_code', $code);

        return redirect()->route('lobby.show', $code);
    }

    /**
     * - toggle a user's ready status
     * - broadcast over lobby channel
     * - UserToggleReady has a listener, which triggers on all members ready when there are 2+
     */
    public function ready(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'boolean',
        ]);

        $code = $request->route('code');

        $user = auth()->user();

        $status = $this->lobbyService->toggleReady($code, $validated['status']);

        broadcast(new UserToggleReady($code, $user, $status));

        return back();
    }

    /**
     * - simple call service & leave
     * - lobby teardown is in service
     */
    public function leave(Request $request): RedirectResponse
    {
        $this->lobbyService->leave(auth()->user(), $request->route('code'));

        $request->session()->pull('lobby_code');

        return redirect()->route('dashboard');
    }
}
