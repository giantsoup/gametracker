<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class GamesList extends Component
{
    use WithPagination;

    public Event $event;

    #[On('gameAdded')]
    #[On('gameRemoved')]
    public function refreshGamesList()
    {
        // This method will be called when the gameAdded or gameRemoved event is dispatched
        // No need to do anything here, Livewire will automatically re-render the component
    }

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function removeGame(Game $game)
    {
        $game->delete();
        $this->dispatch('gameRemoved');
    }

    public function getGamesProperty(): Collection
    {
        return $this->event->games()->with('owners.user')->latest()->get();
    }

    public function render()
    {
        return view('livewire.events.games-list', [
            'games' => $this->games,
        ]);
    }
}
