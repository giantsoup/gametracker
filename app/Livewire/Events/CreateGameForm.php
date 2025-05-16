<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Support\Collection;
use Livewire\Component;

class CreateGameForm extends Component
{
    public Event $event;

    public string $name = '';

    public int $duration = 60; // Default to 60 minutes (1 hour)

    public array $selectedPlayerIds = [];

    public bool $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'duration' => 'required|integer|min:15|multiple_of:15',
        'selectedPlayerIds' => 'array',
        'selectedPlayerIds.*' => 'exists:players,id',
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
        $this->reset(['name', 'duration', 'selectedPlayerIds']);
        $this->duration = 60; // Reset to default
    }

    public function createGame()
    {
        $this->validate();

        $game = Game::create([
            'name' => $this->name,
            'duration' => $this->duration,
            'event_id' => $this->event->id,
        ]);

        if (! empty($this->selectedPlayerIds)) {
            $game->owners()->attach($this->selectedPlayerIds);
        }

        $this->reset(['name', 'duration', 'selectedPlayerIds', 'showForm']);
        $this->duration = 60; // Reset to default
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
