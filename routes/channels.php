<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Models.Game.{id}', function ($game, $id) {
    dump($game);
    return (int) $game->id === (int) $id;
});
