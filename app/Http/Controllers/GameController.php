<?php

namespace App\Http\Controllers;

use App\Events\GameUserCreated;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\InviteLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class GameController extends Controller
{
    public function show(string $id): InertiaResponse|Response
    {
        $game = Game::findOrFail($id);
        $game->hands = Redis::hget("game:$id", 'hands');

        return Inertia::render('Game', [
            'game' => $game,
        ]);
    }

    public function create(Request $request): RedirectResponse
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

        Redis::hset("game:$game->id", 'hands', 0);

        return redirect()
            ->action([self::class, 'show'], ['id' => $game->id]);
    }

    // public function play(Request $request)
    // {
    //     Redis::hincrby("game:{$request->game_id}", 'hands', 1);
    // }
}
