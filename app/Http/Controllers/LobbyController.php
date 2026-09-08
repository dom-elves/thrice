<?php

namespace App\Http\Controllers;

use App\Services\LobbyService;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LobbyController extends Controller
{
    public function show($code): InertiaResponse
    {
        return Inertia::render('Lobby');
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
}
