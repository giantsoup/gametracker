<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    /**
     * Map role values to their corresponding badge colors
     */
    public function getRoleBadge(): array
    {
        $colors = [
            UserRole::ADMIN->value => 'red',
            UserRole::USER->value => 'blue',
            // Add more roles and their colors here as needed
        ];

        $roleValue = $this->role->value;
        $color = $colors[$roleValue] ?? 'zinc';

        return [
            'color' => $color,
            'text' => ucfirst(strtolower($roleValue)),
        ];
    }

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
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the players associated with the user.
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the events that the user has participated in.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'players', 'user_id', 'event_id')
            ->withPivot(['nickname', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * Get the points earned by the user.
     */
    public function earnedPoints(): HasMany
    {
        return $this->hasMany(GamePoint::class, 'player_id');
    }

    /**
     * Get the points assigned by the user.
     */
    public function assignedPoints(): HasMany
    {
        return $this->hasMany(GamePoint::class, 'assigned_by');
    }

    /**
     * Get the points modified by the user.
     */
    public function modifiedPoints(): HasMany
    {
        return $this->hasMany(GamePoint::class, 'last_modified_by');
    }
}
