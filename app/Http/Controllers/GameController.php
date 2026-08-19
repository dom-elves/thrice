<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\InviteLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GameController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $game = Game::create([
            'name' => $validated['name'],
            'password' => $validated['password'],
        ]);

        $inviteLink = InviteLink::create([
            'game_id' => $game->id,
            'user_id' => auth()->user()->id,
            'token' => Str::random(8),
        ]);

        return Inertia::render('Game', [
            'game' => $game,
            'inviteLink' => $inviteLink,
        ]);
    }
}
