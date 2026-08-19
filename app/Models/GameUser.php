<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $game_id
 * @property int $user_id
 * @property int $start_balance
 * @property int $end_balance
 * @property Carbon|null $duration
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['game_id', 'user_id', 'start_balance', 'end_balance', 'duration'])]
class GameUser extends Model
{
    /** @use HasFactory<\Database\Factories\GameUserFactory> */
    use HasFactory;

    /**
     * Game that the game user is in, unique.
     *
     * @return BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * User that the game user represents in a game.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
