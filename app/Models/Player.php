<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'nickname',
        'joined_at',
        'left_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    /**
     * Get the user that the player represents.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that the player is participating in.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Determine if the player has joined the event.
     */
    public function hasJoined(): bool
    {
        return $this->joined_at !== null;
    }

    /**
     * Determine if the player has left the event.
     */
    public function hasLeft(): bool
    {
        return $this->left_at !== null;
    }

    /**
     * Mark the player as having joined the event.
     */
    public function join(): self
    {
        $this->update([
            'joined_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark the player as having left the event.
     */
    public function leave(): self
    {
        $this->update([
            'left_at' => now(),
        ]);

        return $this;
    }

    /**
     * Get the display name for the player.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?? $this->user->name;
    }
}
