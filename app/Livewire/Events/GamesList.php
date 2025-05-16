<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class GamesList extends Component
{
    use WithPagination;

    public Event $event;

    protected $listeners = [
        'gameAdded' => '$refresh',
        'gameRemoved' => '$refresh',
    ];

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
