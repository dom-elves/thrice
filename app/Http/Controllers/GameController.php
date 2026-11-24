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
                $pipe->hmget("game_user:{$gameUserId}", [
                    'user_id',
                    'name',
                ]);
            }
        });

        // if game in progress, also get cards
        if ($game->started) {
            $bottom = Redis::hgetall("bottom:{$code}");
            $middle = Redis::hgetall("middle:{$code}");
            $top = Redis::hgetall("top:{$code}");

            $board = [
                'top' => $top,
                'middle' => $middle,
                'bottom' => $bottom,
            ];
        }

        $gameUser = $game->gameUsers()->where('user_id', auth()->id())->first();

        return Inertia::render('Game', [
            'game' => $game,
            'seats' => $seatData,
            'board' => $board ?? [],
            'gameUser' => fn () => $gameUser,
            'gameUserCards' => fn () => $gameUser
                ? Redis::hmget("game_user:{$gameUser->id}", [
                    'bottom_card_1',
                    'bottom_card_2',
                    'middle_card_1',
                    'middle_card_2',
                    'top_card_1',
                    'top_card_2',
                ])
                : null,
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

    /**
     * Allows a player to lock their hand, after moving their cards.
     */
    public function lock(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'cards' => ['required', 'array', 'size:6'],
            'cards.*' => ['required', 'string'],
        ]);

        dd($validated);
    }
}
