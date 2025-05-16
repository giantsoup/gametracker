<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'active',
        'starts_at',
        'ends_at',
        'started_at',
        'ended_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active events.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now())
            ->orderBy('starts_at');
    }

    /**
     * Scope a query to only include past events.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now())
            ->orderBy('ends_at', 'desc');
    }

    /**
     * Scope a query to only include ongoing events.
     */
    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->where('ends_at', '>=', now())
                    ->orWhereNull('ends_at');
            });
    }

    /**
     * Determine if the event is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    /**
     * Determine if the event has started.
     */
    public function hasStarted(): bool
    {
        return $this->started_at !== null;
    }

    /**
     * Determine if the event has ended.
     */
    public function hasEnded(): bool
    {
        return $this->ended_at !== null;
    }

    /**
     * Start the event.
     */
    public function start(): self
    {
        $this->update([
            'active' => true,
            'started_at' => now(),
        ]);

        return $this;
    }

    /**
     * End the event.
     */
    public function end(): self
    {
        $this->update([
            'active' => false,
            'ended_at' => now(),
        ]);

        return $this;
    }

    /**
     * Get the players participating in the event.
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the users participating in the event.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'players')
            ->withPivot(['nickname', 'joined_at', 'left_at'])
            ->withTimestamps();
    }
}
