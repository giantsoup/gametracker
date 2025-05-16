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
        $player->delete();
        $this->dispatch('playerRemoved');
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
        return $this->event->players()->with('user')->latest()->get();
    }

    public function render()
    {
        return view('livewire.events.players-list', [
            'players' => $this->players,
        ]);
    }
}
