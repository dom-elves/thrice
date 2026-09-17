<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class CloseLobby implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $code,
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * Destroy the 'lobby' set & any user ids that were in the 'ready' set.
     */
    public function handle(): void
    {
        Redis::del(
            "lobby:{$this->code}:user_ids",
            "lobby:{$this->code}:ready_user_ids",
        );
    }
}
