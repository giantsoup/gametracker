<?php

namespace App\Livewire\GamePoints;

use App\Models\Game;
use App\Models\GamePoint;
use Illuminate\Support\Collection;
use Livewire\Component;

class DisplayGamePoints extends Component
{
    /**
     * The game to display points for.
     */
    public Game $game;

    /**
     * Whether to show detailed information.
     */
    public bool $showDetails = false;

    /**
     * Initialize the component with the game.
     */
    public function mount(Game $game): void
    {
        $this->game = $game;
    }

    /**
     * Toggle the display of detailed information.
     */
    public function toggleDetails(): void
    {
        $this->showDetails = ! $this->showDetails;
    }

    /**
     * Get the game points with player information.
     *
     * @return Collection<int, array>
     */
    public function getGamePointsWithPlayers(): Collection
    {
        return GamePoint::where('game_id', $this->game->id)
            ->with(['player', 'assignedBy', 'lastModifiedBy'])
            ->get()
            ->map(function (GamePoint $gamePoint) {
                return [
                    'id' => $gamePoint->id,
                    'player' => $gamePoint->player,
                    'points' => $gamePoint->points,
                    'placement' => $gamePoint->placement,
                    'assigned_by' => $gamePoint->assignedBy,
                    'assigned_at' => $gamePoint->assigned_at,
                    'last_modified_by' => $gamePoint->lastModifiedBy,
                    'last_modified_at' => $gamePoint->last_modified_at,
                ];
            })
            ->sortBy([
                ['placement', 'asc'],
                ['points', 'desc'],
            ]);
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
        return view('livewire.game-points.display-game-points', [
            'gamePoints' => $this->getGamePointsWithPlayers(),
        ]);
    }
}
