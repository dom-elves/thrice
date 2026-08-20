<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\InviteLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Events\GameUserCreated;
use Illuminate\Support\Facades\Redis;

class GameController extends Controller
{
    public function show(Request $request)
    {
        $gameId = $request->route('id');

        dd($request->session());
        
        if (Game::findOrFail($gameId)) {
            return Inertia::render('Game', [
                'game' => json_decode(Redis::get(`game:$gameId`)),
                // 'inviteLink' => $request->session()->get('inviteLink'),
            ]);
        } else {
            return response(404);
        }
    }
    
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        [$game, $gameUser, $inviteLink] = DB::transaction(function () use ($validated) {
            $game = Game::create([
                'name' => $validated['name'],
                'password' => $validated['password'],
            ]);

            $gameUser = GameUser::create([
                'game_id' => $game->id,
                'user_id' => auth()->user()->id,
                'start_balance' => 1000,
            ]);

            $inviteLink = InviteLink::create([
                'game_id' => $game->id,
                'user_id' => auth()->user()->id,
                'token' => Str::random(8),
            ]);

            return [$game, $gameUser, $inviteLink];
        });

        event(new GameUserCreated($gameUser));

        Redis::set(`game:$game->id`, $game->toJson());
        // Redis::set();

        return redirect()
            ->action([self::class, 'show'], ['id' => $game->id]);
            // ->with([
            //     'inviteLink' => $inviteLink,
            // ]);
    }
}
