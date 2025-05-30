<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Player;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class PlayersList extends Component
{
    use WithPagination;

    public Event $event;

    protected $listeners = [
        'playerAdded' => '$refresh',
        'playerRemoved' => '$refresh',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function removePlayer(Player $player)
    {
        // Mark the player as left instead of deleting
        $player->leave();
        $this->dispatch('playerRemoved');
    }

    public function deletePlayer(Player $player)
    {
        // Soft delete the player
        $player->delete();
        $this->dispatch('playerRemoved');
    }

    public function restorePlayer($playerId)
    {
        // Restore a soft-deleted player
        $player = Player::withTrashed()->findOrFail($playerId);
        $player->restore();
        $player->update(['left_at' => null]); // Also clear the left_at timestamp
        $this->dispatch('playerUpdated');
    }

    public function markPlayerLeft(Player $player)
    {
        $player->leave();
        $this->dispatch('playerUpdated');
    }

    public function markPlayerJoined(Player $player)
    {
        $player->join();
        $this->dispatch('playerUpdated');
    }

    public function getPlayersProperty(): Collection
    {
        return $this->event->players()->with('user')->withTrashed()->latest()->get();
    }

    public function render()
    {
        return view('livewire.events.players-list', [
            'players' => $this->players,
        ]);
    }
}
