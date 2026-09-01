<?php

namespace App\Models;

use Database\Factories\GameUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
 * @property bool $in_game
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['game_id', 'user_id', 'start_balance', 'end_balance', 'in_game'])]
class GameUser extends Model
{
    /** @use HasFactory<GameUserFactory> */
    use HasFactory;

    /**
     * Game that the game user is in, unique.
     *
     * @return BelongsTo<Game, $this>
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * User that the game user represents in a game.
     *
     * @return BelongsTo<User, $this>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
