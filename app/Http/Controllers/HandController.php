<?php

namespace App\Http\Controllers;

use App\Models\GameUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HandController extends Controller
{
    /**
     * Allows a player to lock their hand, after moving their cards.
     */
    public function lock(Request $request, GameUser $gameUser): RedirectResponse
    {
        $validated = $request->validate([
            'cards' => ['required', 'array', 'size:6'],
            'cards.*' => ['required', 'string'],
        ]);

        dd($validated);
    }
}
