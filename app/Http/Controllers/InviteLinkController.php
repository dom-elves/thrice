<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InviteLinkController extends Controller
{
    public function show(Request $request, $token)
    {
        // $inviteLink = \App\Models\InviteLink::where('token', $token)->firstOrFail();
        // $game = $inviteLink->game;

        // // Store the game and invite link in the session
        // $request->session()->put('game', $game);
        // $request->session()->put('inviteLink', $inviteLink);

        // return redirect()->route('game.show', ['id' => $game->id]);
    }

    public function create()
    {
        $inviteLink = InviteLink::create([
                'game_id' => $game->id,
                'user_id' => auth()->user()->id,
                'token' => Str::random(8),
            ]);
    }
}
