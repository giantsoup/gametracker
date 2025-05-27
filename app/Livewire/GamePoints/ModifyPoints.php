<?php

namespace App\Livewire\GamePoints;

use App\Models\GamePoint;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModifyPoints extends Component
{
    /**
     * The game point record ID to modify.
     */
    public int $gamePointId;

    /**
     * The game point model instance.
     */
    protected GamePoint $gamePoint;

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
        $this->gamePoint = GamePoint::findOrFail($gamePoint);
        $this->points = $this->gamePoint->points;
        $this->placement = $this->gamePoint->placement;
    }

    /**
     * Update the points record.
     */
    public function updatePoints(): void
    {
        $this->validate();

        $this->gamePoint->update([
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
            'points' => 'required|integer|min:0',
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
            'points.required' => 'Points are required.',
            'points.integer' => 'Points must be a whole number.',
            'points.min' => 'Points cannot be negative.',
            'placement.integer' => 'Placement must be a whole number.',
            'placement.min' => 'Placement must be at least 1.',
        ];
    }

    /**
     * Automatically calculate points based on placement.
     */
    public function calculatePointsFromPlacement(): void
    {
        if ($this->placement === null) {
            return;
        }

        // Apply points based on placement according to business rules
        switch ($this->placement) {
            case 1:
                $this->points = 5;
                break;
            case 2:
                $this->points = 3;
                break;
            case 3:
                $this->points = 1;
                break;
            default:
                $this->points = 0;
                break;
        }
    }

    public function render()
    {
        return view('livewire.game-points.modify-points', [
            'player' => $this->gamePoint->player,
            'game' => $this->gamePoint->game,
        ]);
    }
}
