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

    public array $selectedUserIds = [];

    public bool $showForm = true; // Show the form by default

    protected $rules = [
        'selectedUserIds' => 'array',
    ];

    protected $listeners = [
        'playerUpdated' => '$refresh',
        'playerRemoved' => '$refresh',
        'refresh' => '$refresh',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadSelectedUsers();

        // Hide the form by default if there are any players (even if they're all marked as left or soft-deleted)
        if ($this->event->players()->withTrashed()->count() > 0) {
            $this->showForm = false;
        }
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;

        // If showing the form, reload selected users
        if ($this->showForm) {
            $this->loadSelectedUsers();
        }
    }

    public function loadSelectedUsers()
    {
        // Get all active players for this event
        $activePlayers = $this->event->players()
            ->whereNull('left_at')
            ->get();

        // Set selected user IDs from active players
        $this->selectedUserIds = $activePlayers->pluck('user_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    public function selectAll()
    {
        // Get all user IDs and convert them to strings
        $allUserIds = $this->users->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        // Set the selected user IDs to all user IDs
        $this->selectedUserIds = $allUserIds;

        // Force a UI refresh
        $this->dispatch('refresh');
    }

    public function deselectAll()
    {
        // Clear the selected user IDs using reset
        $this->reset('selectedUserIds');

        // Ensure the array is initialized as empty
        $this->selectedUserIds = [];

        // Force a UI refresh
        $this->dispatch('refresh');
    }

    public function createPlayer()
    {
        $this->validate();

        $addedCount = 0;
        $removedCount = 0;
        $reinstatedCount = 0;

        // Get all current players for this event (including soft-deleted ones)
        $currentPlayers = Player::withTrashed()
            ->where('event_id', $this->event->id)
            ->get()
            ->keyBy('user_id');

        // Process selected users - add new players or reinstate deleted ones
        foreach ($this->selectedUserIds as $userId) {
            $userId = (int) $userId;

            // If player exists
            if (isset($currentPlayers[$userId])) {
                $player = $currentPlayers[$userId];

                // If player was deleted, restore it
                if ($player->trashed()) {
                    $player->restore();
                    $reinstatedCount++;
                }

                // If player was marked as left, mark them as joined again
                if ($player->hasLeft()) {
                    $player->update(['left_at' => null]);
                }

                continue;
            }

            // Create new player
            Player::create([
                'user_id' => $userId,
                'event_id' => $this->event->id,
                'joined_at' => now(),
            ]);

            $addedCount++;
        }

        // Process users that were unchecked - mark them as left
        $currentUserIds = $currentPlayers->pluck('user_id')->map(fn ($id) => (string) $id)->toArray();
        $removedUserIds = array_diff($currentUserIds, $this->selectedUserIds);

        foreach ($removedUserIds as $userId) {
            $player = $currentPlayers[(int) $userId];

            // Only mark as left if not already left
            if (! $player->hasLeft() && ! $player->trashed()) {
                $player->leave();
                $removedCount++;
            }
        }

        // Show appropriate messages
        if ($addedCount > 0) {
            session()->flash('success', "$addedCount player(s) added successfully.");
        }

        if ($reinstatedCount > 0) {
            session()->flash('success', "$reinstatedCount player(s) reinstated successfully.");
        }

        if ($removedCount > 0) {
            session()->flash('warning', "$removedCount player(s) marked as left.");
        }

        // Hide the form after saving changes
        $this->showForm = false;

        // Reload selected users to ensure the array is up-to-date
        $this->loadSelectedUsers();

        $this->dispatch('playerAdded');
    }

    public function getUsersProperty(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function getEventPlayersProperty(): Collection
    {
        return $this->event->players()
            ->with('user')
            ->withTrashed()
            ->get()
            ->keyBy('user_id');
    }

    public function render()
    {
        return view('livewire.events.create-player-form', [
            'users' => $this->users,
            'eventPlayers' => $this->eventPlayers,
        ]);
    }
}
