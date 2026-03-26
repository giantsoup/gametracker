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

    public const int DEFAULT_TOTAL_POINTS = 9;

    public const int DEFAULT_TOTAL_PLACEMENTS = 3;

    /**
     * @var list<int>
     */
    public const array DEFAULT_POINTS_DISTRIBUTION = [5, 3, 1];

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
        'points_distribution',
    ];

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'total_points' => self::DEFAULT_TOTAL_POINTS,
        'points_distribution' => '[5,3,1]',
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
     * @return list<int>
     */
    public static function defaultPointsDistribution(
        int $totalPoints = self::DEFAULT_TOTAL_POINTS,
        int $totalPlacements = self::DEFAULT_TOTAL_PLACEMENTS,
    ): array {
        if (
            $totalPoints === self::DEFAULT_TOTAL_POINTS
            && $totalPlacements === self::DEFAULT_TOTAL_PLACEMENTS
        ) {
            return self::DEFAULT_POINTS_DISTRIBUTION;
        }

        $totalPoints = max(1, $totalPoints);
        $totalPlacements = max(1, $totalPlacements);
        $weights = range($totalPlacements, 1);
        $weightTotal = array_sum($weights);
        $distribution = [];
        $remainders = [];

        foreach ($weights as $index => $weight) {
            $rawPoints = ($totalPoints * $weight) / $weightTotal;
            $distribution[$index] = (int) floor($rawPoints);
            $remainders[$index] = $rawPoints - $distribution[$index];
        }

        $remainingPoints = $totalPoints - array_sum($distribution);
        arsort($remainders);

        foreach (array_keys($remainders) as $index) {
            if ($remainingPoints === 0) {
                break;
            }

            $distribution[$index]++;
            $remainingPoints--;
        }

        rsort($distribution);

        return array_values($distribution);
    }

    /**
     * @return list<int>
     */
    public static function parsePointsDistribution(string $value): array
    {
        $segments = preg_split('/[\s,]+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);

        if ($segments === false) {
            return [];
        }

        return array_values(array_map(static fn (string $points): int => (int) $points, $segments));
    }

    public static function pointsDistributionValidationMessage(mixed $value, int $totalPoints): ?string
    {
        if (! is_string($value)) {
            return 'Points distribution must be entered as comma-separated whole numbers.';
        }

        $segments = preg_split('/[\s,]+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);

        if ($segments === false || $segments === []) {
            return 'Enter at least one placement value in the points distribution.';
        }

        $distribution = [];

        foreach ($segments as $segment) {
            if (filter_var($segment, FILTER_VALIDATE_INT) === false) {
                return 'Points distribution must contain only whole numbers.';
            }

            $points = (int) $segment;

            if ($points < 0) {
                return 'Points distribution cannot contain negative values.';
            }

            $distribution[] = $points;
        }

        if (array_sum($distribution) !== $totalPoints) {
            return "Points distribution must add up to {$totalPoints}.";
        }

        return null;
    }

    /**
     * @param  list<int>  $distribution
     */
    public static function formatPointsDistribution(array $distribution): string
    {
        return implode(', ', $distribution);
    }

    /**
     * @param  list<mixed>  $distribution
     * @return list<int>
     */
    public static function normalizePointsDistribution(array $distribution, int $totalPlacements): array
    {
        $normalizedDistribution = array_values(array_map(
            static fn (mixed $points): int => max(0, (int) $points),
            $distribution,
        ));

        if (count($normalizedDistribution) > $totalPlacements) {
            $normalizedDistribution = array_slice($normalizedDistribution, 0, $totalPlacements);
        }

        if (count($normalizedDistribution) < $totalPlacements) {
            $normalizedDistribution = array_pad($normalizedDistribution, $totalPlacements, 0);
        }

        return $normalizedDistribution;
    }

    /**
     * @param  list<mixed>  $distribution
     */
    public static function pointsDistributionArrayValidationMessage(
        mixed $distribution,
        int $totalPoints,
        int $totalPlacements,
    ): ?string {
        if (! is_array($distribution) || ! array_is_list($distribution)) {
            return 'Points distribution must be a list of placement values.';
        }

        if (count($distribution) !== $totalPlacements) {
            return "Points distribution must include {$totalPlacements} placements.";
        }

        $normalizedDistribution = [];

        foreach ($distribution as $points) {
            if (filter_var($points, FILTER_VALIDATE_INT) === false) {
                return 'Each placement value must be a whole number.';
            }

            $normalizedPoints = (int) $points;

            if ($normalizedPoints < 0) {
                return 'Placement values cannot be negative.';
            }

            $normalizedDistribution[] = $normalizedPoints;
        }

        if (self::sumPointsDistribution($normalizedDistribution) !== $totalPoints) {
            return "Points distribution must add up to {$totalPoints}.";
        }

        return null;
    }

    /**
     * @param  list<int>  $distribution
     */
    public static function sumPointsDistribution(array $distribution): int
    {
        return array_sum($distribution);
    }

    public function pointsForPlacement(?int $placement): int
    {
        if ($placement === null || $placement < 1) {
            return 0;
        }

        return (int) ($this->pointsDistribution()[$placement - 1] ?? 0);
    }

    public function formattedPointsDistribution(): string
    {
        return self::formatPointsDistribution($this->pointsDistribution());
    }

    /**
     * @return list<int>
     */
    public function pointsDistribution(): array
    {
        $distribution = $this->points_distribution;

        if (! is_array($distribution) || $distribution === []) {
            return self::defaultPointsDistribution();
        }

        return array_values(array_map(static fn (mixed $points): int => (int) $points, $distribution));
    }
}
