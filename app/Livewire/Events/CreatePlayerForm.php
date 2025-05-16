<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class CreatePlayerForm extends Component
{
    public Event $event;

    public ?int $userId = null;

    public ?string $nickname = null;

    public bool $showForm = false;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'nickname' => 'nullable|string|max:255',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        $this->reset(['userId', 'nickname']);
    }

    public function createPlayer()
    {
        $this->validate();

        // Check if this user is already a player in this event
        $existingPlayer = Player::where('user_id', $this->userId)
            ->where('event_id', $this->event->id)
            ->first();

        if ($existingPlayer) {
            $this->addError('userId', 'This user is already a player in this event.');

            return;
        }

        $player = Player::create([
            'user_id' => $this->userId,
            'event_id' => $this->event->id,
            'nickname' => $this->nickname,
            'joined_at' => now(),
        ]);

        $this->reset(['userId', 'nickname', 'showForm']);
        $this->dispatch('playerAdded');
    }

    public function getUsersProperty(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.events.create-player-form', [
            'users' => $this->users,
        ]);
    }
}
