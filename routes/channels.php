<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Models.Game.{id}', function ($game, $id) {
    // return (int) $game->id === (int) $id;
    return true;
});

Broadcast::channel('lobby.{code}', function ($user, $code) {
    // add more info where necessary
    return ['id' => $user->id, 'name' => $user->name];
});
