<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class CreateGameForm extends Component
{
    public Event $event;

    public string $name = '';

    public int $duration = 60; // Default to 60 minutes (1 hour)

    public int $total_points = Game::DEFAULT_TOTAL_POINTS;

    public int $total_placements = Game::DEFAULT_TOTAL_PLACEMENTS;

    /**
     * @var list<int>
     */
    public array $points_distribution = Game::DEFAULT_POINTS_DISTRIBUTION;

    public array $selectedPlayerIds = [];

    public bool $showForm = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
        $this->reset(['name', 'duration', 'total_points', 'total_placements', 'points_distribution', 'selectedPlayerIds']);
        $this->duration = 60; // Reset to default
        $this->total_points = Game::DEFAULT_TOTAL_POINTS;
        $this->total_placements = Game::DEFAULT_TOTAL_PLACEMENTS;
        $this->points_distribution = Game::defaultPointsDistribution();
    }

    public function createGame(): void
    {
        $this->validate();

        $this->points_distribution = Game::normalizePointsDistribution(
            $this->points_distribution,
            $this->total_placements,
        );
        $this->total_points = Game::sumPointsDistribution($this->points_distribution);

        $pointsDistributionError = Game::pointsDistributionArrayValidationMessage(
            $this->points_distribution,
            $this->total_points,
            $this->total_placements,
        );

        if ($pointsDistributionError !== null) {
            $this->addError('points_distribution', $pointsDistributionError);

            return;
        }

        $game = Game::create([
            'name' => $this->name,
            'duration' => $this->duration,
            'event_id' => $this->event->id,
            'total_points' => $this->total_points,
            'points_distribution' => $this->points_distribution,
        ]);

        if (! empty($this->selectedPlayerIds)) {
            $game->owners()->attach($this->selectedPlayerIds);
        }

        $this->reset(['name', 'duration', 'total_points', 'total_placements', 'points_distribution', 'selectedPlayerIds', 'showForm']);
        $this->duration = 60; // Reset to default
        $this->total_points = Game::DEFAULT_TOTAL_POINTS;
        $this->total_placements = Game::DEFAULT_TOTAL_PLACEMENTS;
        $this->points_distribution = Game::defaultPointsDistribution();
        $this->dispatch('gameAdded');
    }

    public function regeneratePointsDistribution(): void
    {
        $this->total_points = max(1, (int) $this->total_points);
        $this->total_placements = max(1, (int) $this->total_placements);
        $this->points_distribution = Game::defaultPointsDistribution(
            $this->total_points,
            $this->total_placements,
        );
        $this->resetValidation();
    }

    public function syncTotalPoints(): void
    {
        $this->points_distribution = Game::normalizePointsDistribution(
            $this->points_distribution,
            $this->total_placements,
        );
        $this->total_points = Game::sumPointsDistribution($this->points_distribution);
        $this->resetValidation();
    }

    public function increasePlacementPoints(int $index): void
    {
        $this->points_distribution = Game::normalizePointsDistribution(
            $this->points_distribution,
            $this->total_placements,
        );
        $this->points_distribution[$index] = ($this->points_distribution[$index] ?? 0) + 1;
        $this->total_points = Game::sumPointsDistribution($this->points_distribution);
        $this->resetValidation();
    }

    public function decreasePlacementPoints(int $index): void
    {
        $this->points_distribution = Game::normalizePointsDistribution(
            $this->points_distribution,
            $this->total_placements,
        );
        $this->points_distribution[$index] = max(0, ($this->points_distribution[$index] ?? 0) - 1);
        $this->total_points = Game::sumPointsDistribution($this->points_distribution);
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:15|multiple_of:15',
            'total_points' => 'required|integer|min:1',
            'total_placements' => 'required|integer|min:1',
            'points_distribution' => 'required|array|list',
            'points_distribution.*' => 'required|integer|min:0',
            'selectedPlayerIds' => 'array',
            'selectedPlayerIds.*' => 'exists:players,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'duration.multiple_of' => 'The duration must be in 15-minute intervals.',
            'total_points.min' => 'Total points must be at least 1.',
            'total_placements.min' => 'Total placements must be at least 1.',
        ];
    }

    public function getPlayersProperty(): Collection
    {
        return $this->event->players()->with('user')->get();
    }

    public function render(): View
    {
        return view('livewire.events.create-game-form', [
            'players' => $this->players,
        ]);
    }
}
