<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Redis;

class LobbyService
{
    /**
     * Join a lobby in Redis.
     *
     * @param  User  $user;
     * @param  array<string>  $data;
     */
    public function create($user, $data): void
    {
        Redis::sadd("lobby:{$data['join_code']}:user_ids", $user->id);
    }

    /**
     * Leave a lobby in Redis.
     *
     * @param  User  $user;
     * @param  string  $code;
     */
    public function leave($user, $code): void
    {
        Redis::srem("lobby:{$code}:user_ids", $user->id);

        if (Redis::scard("lobby:{$code}:user_ids") === 0) {
            Redis::del("lobby:{$code}:user_ids");
        }
    }
}
