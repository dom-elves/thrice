<?php

namespace App\Http\Controllers;

use App\Actions\Game\CreateGameUserAction;
use App\Models\Game;
use App\Models\GameUser;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
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

    public function leave(Request $request, Game $game): RedirectResponse
    {
        return redirect('dashboard');
    }

    public function sit(Request $request, Game $game)
    {
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! $gameUser) {
            $gameUser = app(CreateGameUserAction::class)->execute($game->id, $user->id);
        }

        $this->gameService->join($gameUser);

        return redirect()->route('game.show', $game->code);
    }

    public function stand(Request $request, Game $game)
    {
        $gameUser = GameUser::where('game_id', $game->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $this->gameService->leave($gameUser);

        return redirect('dashboard');
    }
}
