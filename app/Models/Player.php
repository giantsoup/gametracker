<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * Get the display name for the player.
     * This method is provided for backward compatibility.
     */
    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    /**
     * Get the games that the player is participating in.
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_player')
            ->withPivot('left_at')
            ->withTimestamps();
    }

    /**
     * Get the status of this player in a specific game.
     *
     * @param  Game  $game  The game to check
     * @return string The status: 'playing', 'left', or 'not_playing'
     */
    public function getGameStatus(Game $game): string
    {
        // Use a static cache to avoid running the same query multiple times
        static $statusCache = [];

        // Create a unique cache key for this player and game
        $cacheKey = $this->id.'-'.$game->id;

        // If we have a cached status, return it
        if (isset($statusCache[$cacheKey])) {
            return $statusCache[$cacheKey];
        }

        // Check if the player is in the game
        $playerInGame = $this->games()->where('game_id', $game->id)->first();

        if (! $playerInGame) {
            $statusCache[$cacheKey] = 'not_playing';

            return 'not_playing';
        }

        // Check if the player has left the game
        if ($playerInGame->pivot->left_at) {
            $statusCache[$cacheKey] = 'left';

            return 'left';
        }

        $statusCache[$cacheKey] = 'playing';

        return 'playing';
    }

    /**
     * Get the status of this player in a specific game as an attribute.
     * This allows for more concise code in blade templates.
     *
     * @param  Game  $game  The game to check
     * @return string The status: 'playing', 'left', or 'not_playing'
     */
    public function getStatusInGame(Game $game): string
    {
        return $this->getGameStatus($game);
    }

    /**
     * Get the status of this player in a game as a dynamic attribute.
     * Usage: $player->status_in_game_123 where 123 is the game ID
     *
     * @param  string  $key  The attribute name
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        // Check if the attribute is a status_in_game_X attribute
        if (strpos($key, 'status_in_game_') === 0) {
            $gameId = substr($key, strlen('status_in_game_'));
            $game = Game::find($gameId);

            if ($game) {
                return $this->getGameStatus($game);
            }

            return null;
        }

        return parent::getAttribute($key);
    }
}
