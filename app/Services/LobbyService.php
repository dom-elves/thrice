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
        Redis::sadd("lobby:{$data['code']}:user_ids", $user->id);

        // at this stage, it's just name, code and password
        // remaining info gets input if game actually starts
        foreach ($data as $field => $value) {
            Redis::hset("game:{$data['code']}", $field, $value);
        }
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
     * Toggle the 'ready' status of a user
     * All users being ready will trigger game start
     *
     * @param  string  $code;
     * @param  bool  $status;
     */
    public function toggleReady($code, $status): bool
    {
        $user = auth()->user();

        if ($status) {
            Redis::sadd("lobby:{$code}:ready_user_ids", $user->id);
        } else {
            Redis::srem("lobby:{$code}:ready_user_ids", $user->id);
        }

        return $status;
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

        if (Redis::sismember("lobby:{$code}:ready_user_ids", $user->id)) {
            Redis::srem("lobby:{$code}:ready_user_ids", $user->id);
        }

        if (! Redis::exists("lobby:{$code}:user_ids")) {
            Redis::hdel("game:{$code}", 'code', 'name', 'password');
        }
    }
}
