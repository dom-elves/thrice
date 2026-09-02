<?php

namespace App\Http\Controllers;

use App\Actions\Game\CreateGameAction;
use App\Actions\Game\CreateGameUserAction;
use App\Events\GameUserJoined;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\InviteLink;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
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
     * If game is exists & not in game, just join
     * Finally if game user is in game, broadcast join event
     */
    public function show(string $gameId): InertiaResponse|RedirectResponse
    {
        $game = Game::findOrFail($gameId);
        $user = auth()->user();
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $gameUser) {
            $createGameUserAction = new CreateGameUserAction(
                app()->make(GameService::class)
            );

            $createGameUserAction->execute($game->id, $user->id);
        } elseif (! $gameUser->in_game) {
            $gameService = new GameService;
            $gameService->joinGame($gameUser);
        } else {
            event(new GameUserJoined($gameUser));
        }

        return Inertia::render('Game', [
            'game' => $game,
        ]);
    }

    public function create(Request $request, CreateGameAction $createGameAction): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        if (! isset($validated['name'])) {
            $validated['name'] = auth()->user()->name."'s Game";
        }

        $game = $createGameAction->execute($validated);

        // $inviteLink = InviteLink::create([
        //     'game_id' => $game->id,
        //     'user_id' => $user->id,
        //     'token' => Str::random(8),
        // ]);

        return redirect()
            ->action([self::class, 'show'], ['id' => $game->id]);
    }

    public function leave(Request $request): RedirectResponse
    {
        $gameUser = GameUser::where('game_id', $request->route('id'))
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $gameService = new GameService;
        $gameService->leaveGame($gameUser);

        return redirect('dashboard');
    }

    // public function play(Request $request)
    // {
    //     Redis::hincrby("game:{$request->game_id}", 'hands', 1);
    // }
}
