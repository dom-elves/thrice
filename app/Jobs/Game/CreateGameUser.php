<?php

namespace App\Jobs\Game;

use App\Models\GameUser;
use App\Services\GameService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class CreateGameUser implements ShouldQueue
{
    use Batchable, Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $gameId,
        public int $userId,
    ) {
        //
    }

    /**
     * Execute the job.
     * Create GameUser in mysql, then 'join game' in redis.
     */
    public function handle(GameService $gameService): void
    {
        DB::transaction(function () use ($gameService) {
            $gameUser = GameUser::create([
                'game_id' => $this->gameId,
                'user_id' => $this->userId,
                'start_balance' => 1000,
            ]);

            DB::afterCommit(fn () => $gameService->join($gameUser));
        });
    }
}
