<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Redis;

class LobbyService
{
    /**
     * Create a lobby in Redis.
     *
     * @param  User  $user;
     * @param  array<string>  $data;
     */
    public function create($user, $data): void
    {
        Redis::sadd("lobby:{$data['join_code']}:user_ids", $user->id);
    }

    /**
     * Join a lobby in Redis.
     *
     * @param  User  $user;
     * @param  string  $code;
     */
    public function join($user, $code): void
    {
        Redis::sadd("lobby:{$code}:user_ids", $user->id);
    }

    /**
     * Set your status to 'ready'
     * All users being ready will trigger game start
     *
     * @param User $user;
     * @param string $code;
     *
     */
    public function ready($user, $code): array
    {
        // put a 1s lock or something on this to prevent race conditions
        Redis::sadd("lobby:{$code}:ready_user_ids", $user->id);

        // sdiff returns an array of values that do not match
        // e.g. if [1,2,3] are user_ids but only [2,3] are ready, it will return [1]
        $allReady = empty(Redis::sdiff("lobby:{$code}:user_ids", "lobby:{$code}:ready_user_ids"));

        $playerCount = Redis::scard("lobby:{$code}:ready_user_ids");

        return [$allReady, $playerCount];
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

        if (Redis::exists("lobby:{$code}:ready_user_ids", $user->id)) {
            Redis::srem("lobby:{$code}:ready_user_ids", $user->id);
        }

        if (Redis::scard("lobby:{$code}:user_ids") === 0) {
            Redis::del("lobby:{$code}:user_ids");
        }
    }
}
