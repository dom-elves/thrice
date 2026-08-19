<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\InviteLink;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Games that the user has participated in.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_user', 'user_id', 'game_id');
    }

    /**
     * Representation of the user in a game, unique per game.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gameUsers()
    {
        return $this->hasMany(GameUser::class);
    }

    /**
     * Invite links created by the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relationship\HasMany
     */
    public function inviteLinks()
    {
        return $this->hasMany(InviteLink::class);
    }
}
