<?php

namespace App\Http\Controllers;

use App\Actions\Game\CreateGameAction;
use App\Actions\Game\Action;
use App\Events\GameUserCreated;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\InviteLink;
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
     * If the user is already in the game, just return the game.
     * Otherwise, create a new GameUser for them first.
     */
    public function show(string $gameId): InertiaResponse|RedirectResponse
    {
        $game = Game::findOrFail($gameId);
        $user = auth()->user();
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->first();

        // maybe here i can just check redis for a user session
        // rather than game->users
        // that way, they can be redirected to the correct game
        // but then again, i need to build proper invite links etc so
        // the games are unique and can't be guessed

        if (! $gameUser) {
            $createGameUserAction = new CreateGameUserAction(
                app()->make(\App\Services\GameService::class)
            );
            
            $createGameUserAction->create($game->id, $user->id);
        } else {
            // todo: either add an identical broadcast or change the name of this
            // depends if further down the line any sort of distinction between
            // game user being created and immediately joining a game
            // or just joining a game

            // change this to redis join
            event(new GameUserCreated($gameUser));
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

    // public function leave(Request $request) {}

    // public function play(Request $request)
    // {
    //     Redis::hincrby("game:{$request->game_id}", 'hands', 1);
    // }
}
