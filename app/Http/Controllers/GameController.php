<?php

namespace App\Http\Controllers;

use App\Actions\Game\CreateGameUserAction;
use App\Models\Game;
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

    /**
     * Take the user to the table of the game
     * - seats are hardcoded in redis like [0 => ''] where empty string is user_id
     * - maps the gameUser data in redis to the relative seats so the above kv becomes [0 => [gameUserData]]
     */
    public function show(Game $game): InertiaResponse
    {
        $code = $game->code;
        $seats = Redis::hgetall("game_seats:{$code}");

        $seatData = Redis::pipeline(function ($pipe) use ($seats) {
            foreach ($seats as $seat => $gameUserId) {
                $pipe->hgetall("game_user:{$gameUserId}");
            }
        });

        return Inertia::render('Game', [
            'game' => $game,
            'seats' => $seatData,
            'gameUser' => fn () => $game->gameUsers()->where('user_id', auth()->id())->first(),
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
     * - 'position' is a param, hardcoded 0-5'
     * - check if user exists already, create if not
     * - sit
     */
    public function sit(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'position' => [
                'required',
                'integer',
                'min:0',
                'max:5',
                function ($attribute, $value, $fail) use ($game) {
                    $seats = Redis::hgetall("game_seats:{$game->code}");
                    if (isset($seats[$value]) && ! empty($seats[$value])) {
                        $fail('You are already sat at the table');
                    }
                }],
        ]);

        $gameUser = $game->gameUsers()->where('user_id', auth()->id())->first();

        if (! $gameUser) {
            $gameUser = app(CreateGameUserAction::class)->execute($game->id, (int) auth()->id());
        }

        $this->gameService->sit($gameUser, $validated['position']);

        return back();
    }

    /**
     * Stand at the table (stop playing, just spectate)
     * - basically the same as sit()
     * - though instead of logic for creating a game user, there'll be bits for settling balances
     */
    public function stand(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'position' => ['required', 'integer', 'min:0', 'max:5'],
        ]);

        $gameUser = $game->gameUsers()->where('user_id', auth()->id())->first();

        $this->gameService->stand($gameUser, $validated['position']);

        return back();
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
