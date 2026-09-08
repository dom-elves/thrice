<?php

namespace App\Services;

use App\Events\Lobby\UserJoined;
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

        // todo: find out why this is supposedly not needed
        // event(new UserJoined($data['join_code']));
    }

    /**
     * Leave a lobby in Redis.
     *
     * @param User $user;
     * @param string $code;
     */
    public function leave($user, $code): void
    {
        Redis::srem("lobby:{$code}:user_ids", $user->id);

        if (Redis::scard("lobby:{$code}:user_ids") === 0) {
            Redis::del("lobby:{$code}:user_ids");
        }
    }
}
