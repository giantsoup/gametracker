<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'rules',
        'duration',
        'event_id',
        'total_points',
        'points_recipients',
        'points_distribution',
        'started_at',
        'stopped_at',
        'finished_at',
        'accumulated_duration',
        'status',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'total_points' => 'integer',
            'points_recipients' => 'integer',
            'points_distribution' => 'array',
            'started_at' => 'datetime',
            'stopped_at' => 'datetime',
            'finished_at' => 'datetime',
            'accumulated_duration' => 'integer',
            'status' => GameStatus::class,
        ];
    }

    /**
     * Get the event that the game belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the players who own this game.
     */
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'game_player')
            ->withTimestamps();
    }

    /**
     * Get all players who are playing this game.
     * This includes players who are not owners but are part of the event.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'game_player')
            ->withPivot('left_at')
            ->withTimestamps();
    }

    /**
     * Get all active players who are playing this game (not left).
     */
    public function activePlayers(): BelongsToMany
    {
        return $this->players()
            ->wherePivotNull('left_at');
    }

    /**
     * Check if a player is playing this game.
     */
    public function isPlayerPlaying(Player $player): bool
    {
        return $player->getGameStatus($this) === 'playing';
    }

    /**
     * Get the status of a player in this game.
     *
     * @param  Player  $player  The player to check
     * @return string The status: 'playing', 'left', or 'not_playing'
     *
     * @deprecated Use Player::getGameStatus() instead
     */
    public function getPlayerStatus(Player $player): string
    {
        // Forward to the new method on the Player model
        return $player->getGameStatus($this);
    }

    /**
     * Mark a player as having left the game.
     */
    public function markPlayerLeft(Player $player): bool
    {
        if ($player->getGameStatus($this) === 'not_playing') {
            return false;
        }

        return (bool) $this->players()->updateExistingPivot($player->id, [
            'left_at' => now(),
        ]);
    }

    /**
     * Mark a player as active in the game (not left).
     */
    public function markPlayerActive(Player $player): bool
    {
        if ($player->getGameStatus($this) === 'not_playing') {
            return false;
        }

        return (bool) $this->players()->updateExistingPivot($player->id, [
            'left_at' => null,
        ]);
    }

    /**
     * Get the duration in a human-readable format.
     */
    public function getDurationForHumans(): string
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get the points associated with this game.
     */
    public function points(): HasMany
    {
        return $this->hasMany(GamePoint::class);
    }

    /**
     * Alias for the points() relationship.
     *
     * This method provides backward compatibility with code that uses gamePoints.
     */
    public function gamePoints(): HasMany
    {
        return $this->points();
    }

    /**
     * Get the default points distribution.
     *
     * @return array<int, int>
     */
    public static function getDefaultPointsDistribution(): array
    {
        return [
            1 => 6, // 1st place: 6 points
            2 => 4, // 2nd place: 4 points
            3 => 3, // 3rd place: 3 points
            4 => 1, // 4th place: 1 point
            5 => 1, // 5th place: 1 point
        ];
    }

    /**
     * Get the points for a specific placement.
     */
    public function getPointsForPlacement(int $placement): int
    {
        if ($this->points_distribution === null) {
            $defaultDistribution = self::getDefaultPointsDistribution();

            return $defaultDistribution[$placement] ?? 0;
        }

        return $this->points_distribution[$placement] ?? 0;
    }

    /**
     * Generate a points distribution based on the total points and number of recipients.
     *
     * @return array<int, int>
     */
    public function generatePointsDistribution(): array
    {
        if ($this->points_recipients <= 0) {
            return [];
        }

        // If we already have a custom distribution, return it
        if ($this->points_distribution !== null) {
            return $this->points_distribution;
        }

        // Otherwise, generate a distribution based on the default pattern
        $distribution = [];
        $remainingPoints = $this->total_points;

        // Use a decreasing pattern similar to the default (5, 3, 1)
        for ($i = 1; $i <= $this->points_recipients; $i++) {
            if ($i === $this->points_recipients) {
                // Last recipient gets all remaining points
                $distribution[$i] = $remainingPoints;
            } else {
                // Calculate points for this placement
                $points = max(1, floor($remainingPoints * 0.5));
                $distribution[$i] = $points;
                $remainingPoints -= $points;
            }

            // If we've distributed all points, stop
            if ($remainingPoints <= 0) {
                break;
            }
        }

        return $distribution;
    }

    /**
     * Start the game timer.
     *
     * @return bool Whether the game was successfully started
     */
    public function startGame(): bool
    {
        // If the game is already running, don't do anything
        if ($this->isRunning()) {
            return false;
        }

        $this->started_at = now();
        $this->stopped_at = null;

        // Update status to Playing if it's currently Ready
        if ($this->status === GameStatus::Ready) {
            $this->status = GameStatus::Playing;
        }

        return $this->save();
    }

    /**
     * Stop the game timer and update the accumulated duration.
     *
     * @return bool Whether the game was successfully stopped
     */
    public function stopGame(): bool
    {
        // If the game is not running, don't do anything
        if (! $this->isRunning()) {
            return false;
        }

        $this->stopped_at = now();

        // Calculate the duration of this session in minutes
        $sessionDuration = $this->started_at->diffInMinutes($this->stopped_at);

        // Add to the accumulated duration
        $this->accumulated_duration += $sessionDuration;

        return $this->save();
    }

    /**
     * Mark the game as ready to start.
     *
     * @return bool Whether the status was successfully updated
     */
    public function markAsReady(): bool
    {
        // Only background games or new games can be marked as ready
        if ($this->status !== null && $this->status !== GameStatus::Background) {
            return false;
        }

        $this->status = GameStatus::Ready;

        return $this->save();
    }

    /**
     * Mark the game as currently playing.
     *
     * @return bool Whether the status was successfully updated
     */
    public function markAsPlaying(): bool
    {
        // Validate the status transition
        if ($this->status !== null && ! $this->status->canTransitionTo(GameStatus::Playing)) {
            return false;
        }

        $this->status = GameStatus::Playing;

        // Start the game timer if it's not already running
        if (! $this->isRunning()) {
            $this->started_at = now();
            $this->stopped_at = null;
        }

        return $this->save();
    }

    /**
     * Mark the game as finished.
     *
     * @return bool Whether the status was successfully updated
     */
    public function markAsFinished(): bool
    {
        // Validate the status transition
        if ($this->status !== null && ! $this->status->canTransitionTo(GameStatus::Finished)) {
            return false;
        }

        // Stop the game timer if it's running
        if ($this->isRunning()) {
            $this->stopGame();
        }

        $this->status = GameStatus::Finished;
        $this->finished_at = now();

        return $this->save();
    }

    /**
     * Mark the game as a background game.
     *
     * @return bool Whether the status was successfully updated
     */
    public function markAsBackground(): bool
    {
        $this->status = GameStatus::Background;

        return $this->save();
    }

    /**
     * Check if the game is currently running.
     */
    public function isRunning(): bool
    {
        return $this->started_at !== null && $this->stopped_at === null;
    }

    /**
     * Get the current session duration in minutes (if the game is running).
     */
    public function getCurrentSessionDuration(): int
    {
        if (! $this->isRunning()) {
            return 0;
        }

        return $this->started_at->diffInMinutes(now());
    }

    /**
     * Get the total duration (accumulated + current session if running) in minutes.
     */
    public function getTotalDuration(): int
    {
        return $this->accumulated_duration + $this->getCurrentSessionDuration();
    }

    /**
     * Get the total duration in a human-readable format.
     */
    public function getTotalDurationForHumans(): string
    {
        $totalMinutes = $this->getTotalDuration();
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabel(): string
    {
        return $this->status ? $this->status->label() : 'Unplayed';
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColor(): string
    {
        return $this->status ? $this->status->color() : 'zinc';
    }

    /**
     * Set the game status.
     *
     * @param  GameStatus  $status  The new status
     * @return bool Whether the status was successfully updated
     */
    public function setStatus(GameStatus $status): bool
    {
        $this->status = $status;

        return $this->save();
    }
}
