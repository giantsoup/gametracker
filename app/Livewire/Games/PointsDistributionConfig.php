<?php

namespace App\Livewire\Games;

use App\Models\Game;
use Livewire\Component;

class PointsDistributionConfig extends Component
{
    /**
     * The game to configure points distribution for.
     */
    public ?Game $game = null;

    /**
     * The total points to distribute.
     */
    public int $totalPoints = 9;

    /**
     * The number of players who will receive points.
     */
    public int $pointsRecipients = 3;

    /**
     * The points distribution array.
     *
     * @var array<int, int>
     */
    public array $pointsDistribution = [];

    /**
     * Whether to use a custom distribution or auto-generate.
     */
    public bool $useCustomDistribution = false;

    /**
     * Initialize the component.
     */
    public function mount(?Game $game = null): void
    {
        $this->game = $game;

        if ($game) {
            $this->totalPoints = $game->total_points ?? $this->totalPoints;
            $this->pointsRecipients = $game->points_recipients ?? $this->pointsRecipients;
            $this->pointsDistribution = $game->points_distribution ?? [];
            $this->useCustomDistribution = $game->points_distribution !== null;
        } else {
            // Initialize with default values
            $this->pointsDistribution = Game::getDefaultPointsDistribution();
        }

        // If no distribution is set, generate one
        if (empty($this->pointsDistribution)) {
            $this->generateDistribution();
        }

        // Emit initial configuration
        $this->emitPointsDistributionUpdated();
    }

    /**
     * Generate a points distribution based on the total points and number of recipients.
     */
    public function generateDistribution(): void
    {
        $tempGame = new Game([
            'total_points' => $this->totalPoints,
            'points_recipients' => $this->pointsRecipients,
        ]);

        $this->pointsDistribution = $tempGame->generatePointsDistribution();
    }

    /**
     * Update the points distribution when the total points or number of recipients changes.
     */
    public function updatedTotalPoints(): void
    {
        if (! $this->useCustomDistribution) {
            $this->generateDistribution();
        }

        $this->emitPointsDistributionUpdated();
    }

    /**
     * Update the points distribution when the number of recipients changes.
     */
    public function updatedPointsRecipients(): void
    {
        if (! $this->useCustomDistribution) {
            $this->generateDistribution();
        } else {
            // Ensure we have the right number of entries in the distribution
            $currentCount = count($this->pointsDistribution);

            if ($currentCount < $this->pointsRecipients) {
                // Add entries for new recipients
                for ($i = $currentCount + 1; $i <= $this->pointsRecipients; $i++) {
                    $this->pointsDistribution[$i] = 0;
                }
            } elseif ($currentCount > $this->pointsRecipients) {
                // Remove excess entries
                for ($i = $this->pointsRecipients + 1; $i <= $currentCount; $i++) {
                    unset($this->pointsDistribution[$i]);
                }
            }
        }

        $this->emitPointsDistributionUpdated();
    }

    /**
     * Toggle between custom and auto-generated distribution.
     */
    public function toggleCustomDistribution(): void
    {
        $this->useCustomDistribution = ! $this->useCustomDistribution;

        if (! $this->useCustomDistribution) {
            $this->generateDistribution();
        }

        $this->emitPointsDistributionUpdated();
    }

    /**
     * Emit an event with the current points distribution configuration.
     */
    private function emitPointsDistributionUpdated(): void
    {
        $this->dispatch('points-distribution-updated', $this->getGameData());
    }

    /**
     * Update the points distribution when a specific placement's points change.
     */
    public function updatedPointsDistribution(): void
    {
        $this->emitPointsDistributionUpdated();
    }

    /**
     * Get the sum of all points in the distribution.
     */
    public function getPointsSum(): int
    {
        return array_sum($this->pointsDistribution);
    }

    /**
     * Check if the points sum matches the total points.
     */
    public function getIsValidDistribution(): bool
    {
        return $this->getPointsSum() === $this->totalPoints;
    }

    /**
     * Get the game data for saving.
     *
     * @return array<string, mixed>
     */
    public function getGameData(): array
    {
        return [
            'total_points' => $this->totalPoints,
            'points_recipients' => $this->pointsRecipients,
            'points_distribution' => $this->useCustomDistribution ? $this->pointsDistribution : null,
        ];
    }

    public function render()
    {
        return view('livewire.games.points-distribution-config', [
            'pointsSum' => $this->getPointsSum(),
            'isValid' => $this->getIsValidDistribution(),
        ]);
    }
}
