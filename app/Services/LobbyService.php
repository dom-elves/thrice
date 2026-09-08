<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Redis;

class LobbyService
{
    /**
     * Join a lobby in Redis.
     *
     * @param User $user;
     * @param array $data;
     */
    public function create($user, $data): void
    {
        Redis::sadd("lobby:{$data['join_code']}:user_ids", $user->id);

        // lobby created->user joined
    }
}
