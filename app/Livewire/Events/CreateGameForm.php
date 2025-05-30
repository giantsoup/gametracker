<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateGameForm extends Component
{
    public Event $event;

    public string $name = '';

    public int $duration = 60; // Default to 60 minutes (1 hour)

    public array $selectedPlayerIds = [];

    public bool $showForm = false;

    public int $totalPoints = 15; // Default total points

    public int $pointsRecipients = 5; // Default number of recipients

    public ?array $pointsDistribution = null; // Custom points distribution

    protected $rules = [
        'name' => 'required|string|max:255',
        'duration' => 'required|integer|min:15|multiple_of:15',
        'selectedPlayerIds' => 'array',
        'selectedPlayerIds.*' => 'exists:players,id',
        'totalPoints' => 'required|integer|min:1',
        'pointsRecipients' => 'required|integer|min:1',
        'pointsDistribution' => 'nullable|array',
    ];

    protected $messages = [
        'duration.multiple_of' => 'The duration must be in 15-minute intervals.',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        $this->reset(['name', 'duration', 'selectedPlayerIds', 'totalPoints', 'pointsRecipients', 'pointsDistribution']);
        $this->duration = 60; // Reset to default
        $this->totalPoints = 15; // Reset to default
        $this->pointsRecipients = 5; // Reset to default
    }

    /**
     * Receive points distribution configuration from the PointsDistributionConfig component.
     */
    #[On('points-distribution-updated')]
    public function updatePointsDistribution($data)
    {
        $this->totalPoints = $data['total_points'];
        $this->pointsRecipients = $data['points_recipients'];
        $this->pointsDistribution = $data['points_distribution'];
    }

    public function createGame()
    {
        $this->validate();

        // Check if there are any players in the event
        if ($this->event->players()->count() === 0) {
            $this->addError('selectedPlayerIds', 'You need to add players to the event before creating games.');

            return;
        }

        $game = Game::create([
            'name' => $this->name,
            'duration' => $this->duration,
            'event_id' => $this->event->id,
            'total_points' => $this->totalPoints,
            'points_recipients' => $this->pointsRecipients,
            'points_distribution' => $this->pointsDistribution,
        ]);

        if (! empty($this->selectedPlayerIds)) {
            $game->owners()->attach($this->selectedPlayerIds);
        }

        $this->reset(['name', 'duration', 'selectedPlayerIds', 'totalPoints', 'pointsRecipients', 'pointsDistribution', 'showForm']);
        $this->duration = 60; // Reset to default
        $this->totalPoints = 15; // Reset to default
        $this->pointsRecipients = 5; // Reset to default
        $this->dispatch('gameAdded');
    }

    public function getPlayersProperty(): Collection
    {
        return $this->event->players()->with('user')->get();
    }

    public function render()
    {
        return view('livewire.events.create-game-form', [
            'players' => $this->players,
        ]);
    }
}
