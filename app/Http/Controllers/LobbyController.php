<?php

namespace App\Http\Controllers;

use App\Events\Lobby\UserToggleReady;
use App\Services\LobbyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LobbyController extends Controller
{
    public function __construct(
        private LobbyService $lobbyService
    ) {}

    /**
     * Keeping controllers thin where possible,
     * middleware now handles all sort of logic around lobby existence, capacity, and if the user is in it
     * this way, we can progress to show() from create() without extra steps
     */
    public function show(Request $request): InertiaResponse
    {
        $code = $request->route('code');

        return Inertia::render('Lobby', [
            'code' => $code,
        ]);
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
