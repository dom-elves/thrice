<?php

namespace App\Http\Controllers;

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

        if ($full && ! $member) {
            Inertia::flash([
                'message' => 'Lobby is full',
            ]);

            return redirect('dashboard');
        }

        if (! $member) {
            $this->lobbyService->join($user, $code);
        }

        return Inertia::render('Lobby', [
            'code' => $code,
        ]);
    }

    /**
     * - validate data
     * - set a name if one isn't given
     * - generate lobby code
     * - append code to $data
     * - create lobby
     */
    public function create(Request $request): RedirectResponse
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

        $this->lobbyService->create(auth()->user(), $data);

        return redirect()->route('lobby.show', $code);
    }

    /**
     * - simple call service & leave
     * - lobby teardown is in service
     */
    public function leave(Request $request, string $code): RedirectResponse
    {
        $this->lobbyService->leave(auth()->user(), $code);

        return redirect()->route('dashboard');
    }
}
