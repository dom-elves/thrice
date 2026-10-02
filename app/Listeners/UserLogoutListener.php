<?php

namespace App\Listeners;

use App\Models\Game;
use App\Models\User;
use App\Services\GameService;
use App\Services\LobbyService;
use Illuminate\Auth\Events\Logout;

class UserLogoutListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;

        $lobby_code = session('lobby_code');

        if ($lobby_code && $user instanceof User) {
            $lobbyService = app(LobbyService::class);
            $lobbyService->leave($user, $lobby_code);
        }

        // $game_code = session('game_code');
        
        // if ($game_code && $user instanceof User) {
        //     // this is now feels really bad and will have to be improved later
        //     $game = Game::where('code', $game_code)->first();
        //     $gameUser = $game->gameUsers->where('user_id', $user->id)->first();
            
        //     $gameService = app(GameService::class);
        //     $gameService->leave($gameUser);
        // }
    }
}
