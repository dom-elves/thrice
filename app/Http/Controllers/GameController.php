<?php

namespace App\Http\Controllers;

use App\Actions\Game\CreateGameUserAction;
use App\Models\Game;
use App\Models\GameUser;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class GameController extends Controller
{
    public function show(Game $game): InertiaResponse
    {
        $user = auth()->user();
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $gameUser) {
            $gameUser = app(CreateGameUserAction::class)->execute($game->id, $user->id);
        }

        if (! Redis::exists("game_user:{$gameUser->id}")) {
            $gameService = app(GameService::class);
            $gameService->join($gameUser);
        }

        return Inertia::render('Game', [
            'game' => $game,
            'gameUser' => $gameUser,
            'players' => $game->gameUsers()
                ->where('in_game', true)
                ->with('user')
                ->get(),
        ]);
    }

    public function leave(Request $request, Game $game): RedirectResponse
    {
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $gameService = app(GameService::class);
        $gameService->leave($gameUser);

        $request->session()->pull('game_code');

        return redirect('dashboard');
    }

    // public function play(Request $request)
    // {
    //     Redis::hincrby("game:{$request->game_id}", 'hands', 1);
    // }
}
