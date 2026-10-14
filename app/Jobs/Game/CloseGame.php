<?php

namespace App\Jobs;

use App\Models\Game;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class CloseGame implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $gameId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * If the game never event started, delete it.
     * Otherwise, record hand total & finish.
     */
    public function handle(): void
    {
        $game = Game::findOrFail($this->gameId);
        $key = "game:{$game->code}";

        $gameState = Redis::hgetall($key);

        if (! $game->started) {
            $game->delete();
        } else {
            $game->update([
                'hands' => $gameState['hands'],
                'finished' => 1,
            ]);
        }

        Redis::del($key);
    }
}
