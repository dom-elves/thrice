<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $password
 * @property int|string|null $hands
 * @property bool $finished
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'hands', 'finished'])]
#[Hidden(['password'])]

class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory, Notifiable;

    /**
     * Users from a given game.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'game_users', 'game_id', 'user_id');
    }

    /**
     * Representation of the user in a game, unique per game.
     *
     * @return HasMany<GameUser, $this>
     */
    public function gameUsers()
    {
        return $this->hasMany(GameUser::class);
    }

    /**
     * Invite link generated for the game.
     *
     * @return HasOne<InviteLink, $this>
     */
    public function inviteLink()
    {
        return $this->hasOne(InviteLink::class);
    }
}
