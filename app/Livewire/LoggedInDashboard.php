<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Http\Request;
use Livewire\Component;

class LoggedInDashboard extends Component
{
    public $activeEventId = null; // Currently selected event ID

    public $events = []; // All active events

    public function mount(Request $request)
    {
        // Get all active events
        $this->events = Event::active()->get();

        // Check if event is specified in query parameters
        if ($request->has('event')) {
            $eventId = (int) $request->input('event');
            // Check if the event exists in our active events
            if ($this->events->contains('id', $eventId)) {
                $this->activeEventId = $eventId;
            }
        }

        // If no event is selected or the selected event doesn't exist, use the first active event
        if (! $this->activeEventId && $this->events->isNotEmpty()) {
            $this->activeEventId = $this->events->first()->id;
        }
    }

    public function render()
    {
        // Get all active events
        $events = $this->events;

        // Get the current active event
        $activeEvent = $events->firstWhere('id', $this->activeEventId);

        // If no active event is found, use the first one (if available)
        if (! $activeEvent && $events->isNotEmpty()) {
            $activeEvent = $events->first();
            $this->activeEventId = $activeEvent->id;
        }

        // Get current active game if there is an active event
        $currentGame = null;
        $gameDuration = null;
        $finishedGames = collect();
        $upcomingGames = collect();

        if ($activeEvent) {
            // Get the current active game (most recent)
            $currentGame = $activeEvent->games()->latest()->first();

            if ($currentGame && $activeEvent->started_at) {
                // Calculate how long the current game has been going on
                $gameDuration = now()->diffForHumans($activeEvent->started_at, true);
            }

            // Get finished games for the current event (excluding the current game)
            $finishedGames = $activeEvent->games()
                ->when($currentGame, function ($query) use ($currentGame) {
                    return $query->where('id', '!=', $currentGame->id);
                })
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Get upcoming games for the current event
            // Since there's no specific field to determine if a game is upcoming,
            // we'll assume games without a created_at timestamp are upcoming
            $upcomingGames = $activeEvent->games()
                ->whereNull('created_at')
                ->orderBy('id', 'asc')
                ->take(5)
                ->get();
        }

        return view('livewire.logged-in-dashboard', [
            'events' => $events,
            'activeEvent' => $activeEvent,
            'currentGame' => $currentGame,
            'gameDuration' => $gameDuration,
            'finishedGames' => $finishedGames,
            'upcomingGames' => $upcomingGames,
        ]);
    }

    public function switchEvent($eventId)
    {
        // Check if the event exists in our active events
        if ($this->events->contains('id', $eventId)) {
            $this->activeEventId = $eventId;
        }
    }
}
