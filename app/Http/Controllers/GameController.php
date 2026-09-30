<?php

namespace App\Http\Controllers;

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
    /**
     * show() method assumes the user has made it past the checks in CheckGameStatus middleware.
     * It checks if the game exists, is finished, is full, then if the user is already in the game.
     * These checks then assume the game is active and with an empty space.
     *
     * If game user does not exist, create & join
     * If game exists & user is not in game, join
     * Otherwise, just return the game
     */
    public function show(Game $game): InertiaResponse|RedirectResponse
    {
        $code = $request->route('code');

        return Inertia::render('Game', [
            'game' => $game,
        ]);
    }

    public function leave(Request $request): RedirectResponse
    {
        $gameUser = GameUser::where('game_id', $request->route('id'))
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $gameService = new GameService;
        // $gameService->leaveGame($gameUser);

        return redirect('dashboard');
    }

    // public function play(Request $request)
    // {
    //     Redis::hincrby("game:{$request->game_id}", 'hands', 1);
    // }
}
