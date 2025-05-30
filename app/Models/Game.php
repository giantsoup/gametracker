<?php

namespace App\Models;

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
        'duration',
        'event_id',
        'total_points',
        'points_recipients',
        'points_distribution',
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
}
