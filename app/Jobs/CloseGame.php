<?php

namespace App\Jobs;

use App\Models\Game;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class CloseGame implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $gameId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $game = Game::findOrFail($this->gameId);
        // todo: remove game redis hash and record data in mysql db
        $game->delete();
    }
}
