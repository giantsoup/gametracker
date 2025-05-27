<?php

namespace App\Livewire\GamePoints;

use App\Models\GamePoint;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class PlayerTotalPoints extends Component
{
    /**
     * The user to display total points for.
     */
    public User $user;

    /**
     * Whether to show detailed breakdown.
     */
    public bool $showBreakdown = false;

    /**
     * Initialize the component with the user.
     */
    public function mount(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Toggle the display of the detailed breakdown.
     */
    public function toggleBreakdown(): void
    {
        $this->showBreakdown = ! $this->showBreakdown;
    }

    /**
     * Get the total points for the user.
     */
    public function getTotalPoints(): int
    {
        return $this->user->earnedPoints()->sum('points');
    }

    /**
     * Get the points breakdown by game.
     *
     * @return Collection<int, array>
     */
    public function getPointsBreakdown(): Collection
    {
        return $this->user->earnedPoints()
            ->with('game')
            ->get()
            ->map(function (GamePoint $gamePoint) {
                return [
                    'id' => $gamePoint->id,
                    'game' => $gamePoint->game,
                    'points' => $gamePoint->points,
                    'placement' => $gamePoint->placement,
                ];
            })
            ->sortByDesc('points');
    }

    /**
     * Get the placement statistics.
     *
     * @return array<string, int>
     */
    public function getPlacementStats(): array
    {
        $placements = $this->user->earnedPoints()
            ->whereNotNull('placement')
            ->pluck('placement')
            ->countBy();

        return [
            'first' => $placements[1] ?? 0,
            'second' => $placements[2] ?? 0,
            'third' => $placements[3] ?? 0,
        ];
    }

    /**
     * Listen for the points-saved and points-updated events.
     */
    protected function getListeners(): array
    {
        return [
            'points-saved' => '$refresh',
            'points-updated' => '$refresh',
        ];
    }

    public function render()
    {
        return view('livewire.game-points.player-total-points', [
            'totalPoints' => $this->getTotalPoints(),
            'pointsBreakdown' => $this->showBreakdown ? $this->getPointsBreakdown() : null,
            'placementStats' => $this->getPlacementStats(),
        ]);
    }
}
