<?php

namespace App\Http\Controllers;

use App\Actions\Game\CreateGameUserAction;
use App\Models\Game;
use App\Models\GameUser;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class GameController extends Controller
{
    public function __construct(
        private GameService $gameService
    ) {}

    public function show(Game $game): InertiaResponse
    {
        return Inertia::render('Game', [
            'game' => $game,
            'gameUser' => $game->gameUsers->where('user_id', auth()->id())->first() ?? null,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'private' => 'sometimes|boolean',
        ]);

        if (! isset($validated['name'])) {
            $validated['name'] = auth()->user()->name."'s Game";
        }

        if (! isset($validated['password'])) {
            $validated['password'] = '';
        }

        if (! isset($validated['private'])) {
            $validated['private'] = 0;
        }

        $code = Str::lower(Str::random(12));

        $validated['code'] = $code;

        $game = $this->gameService->create($validated);

        $request->session()->put('game_code', $code);

        return redirect()->route('game.show', $code);
    }

    /**
     * Sit at the table.
     * - query user from id, only id is passed as param to avoid editing any other attrbiutes
     * - check if user exists already, create if not
     * - sit
     */
    public function sit(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'gameUserId' => 'nullable|exists:game_users,id',
        ]);

        if (! isset($validated['gameUserId'])) {
            $gameUser = app(CreateGameUserAction::class)->execute($game->id, (int) auth()->id());
        } else {
            $gameUser = GameUser::where('id', $validated['gameUserId'])->first();
        }

        $this->gameService->sit($gameUser);

        // todo: check for 2+ players being ready

        return redirect()->route('game.show', $game->code);
    }

    /**
     * Stand at the table (stop playing, just spectate)
     * - basically the same as sit()
     * - though instead of logic for creating a game user, there'll be bits for settling balances
     */
    public function stand(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'gameUserId' => 'exists:game_users,id',
        ]);

        $gameUser = GameUser::where('id', $validated['gameUserId'])->first();

        $this->gameService->stand($gameUser);

        return redirect()->route('game.show', $game->code);
    }

    /**
     * Returns to dash.
     * - leaves game if user is in
     */
    public function leave(Request $request, Game $game): RedirectResponse
    {
        return redirect('dashboard');
    }
}
