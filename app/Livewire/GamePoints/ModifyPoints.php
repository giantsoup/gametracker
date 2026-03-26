<?php

namespace App\Livewire\GamePoints;

use App\Models\Game;
use App\Models\GamePoint;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModifyPoints extends Component
{
    /**
     * The game point record ID to modify.
     */
    public int $gamePointId;

    /**
     * The player attached to the points record.
     */
    public User $player;

    /**
     * The game attached to the points record.
     */
    public Game $game;

    /**
     * The points value.
     */
    public int $points;

    /**
     * The placement value.
     */
    public ?int $placement = null;

    /**
     * Initialize the component with the game point ID.
     */
    public function mount(int $gamePoint): void
    {
        $this->gamePointId = $gamePoint;
        $gamePointRecord = GamePoint::with('game')->findOrFail($gamePoint);
        $this->player = $gamePointRecord->player;
        $this->game = $gamePointRecord->game;
        $this->points = $gamePointRecord->points;
        $this->placement = $gamePointRecord->placement;
    }

    /**
     * Update the points record.
     */
    public function updatePoints(): void
    {
        $this->validate();

        $gamePoint = GamePoint::findOrFail($this->gamePointId);

        if ($this->placement !== null) {
            $placementAlreadyAssigned = GamePoint::query()
                ->where('game_id', $gamePoint->game_id)
                ->where('placement', $this->placement)
                ->whereKeyNot($gamePoint->id)
                ->exists();

            if ($placementAlreadyAssigned) {
                $this->addError('placement', 'Each placement may only be assigned once per game.');

                return;
            }
        }

        if ($this->placement === null) {
            $gamePoint->delete();
            $this->dispatch('points-updated');

            return;
        }

        $this->points = $this->game->pointsForPlacement($this->placement);

        $gamePoint->update([
            'points' => $this->points,
            'placement' => $this->placement,
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);

        $this->dispatch('points-updated');
    }

    /**
     * Define validation rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'placement' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Define custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'placement.integer' => 'Placement must be a whole number.',
            'placement.min' => 'Placement must be at least 1.',
        ];
    }

    /**
     * Automatically calculate points based on placement.
     */
    public function calculatePointsFromPlacement(): void
    {
        $gamePoint = GamePoint::with('game')->findOrFail($this->gamePointId);

        $this->points = $gamePoint->game->pointsForPlacement($this->placement);
    }

    public function render()
    {
        return view('livewire.game-points.modify-points', [
            'player' => $this->player,
            'game' => $this->game,
        ]);
    }
}
