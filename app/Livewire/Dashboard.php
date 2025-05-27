<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Game;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class Dashboard extends Component
{
    public $activeLayout = 1; // Default layout

    public function render()
    {
        // Get the current active event
        $activeEvent = Event::active()->first();

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

        return view('livewire.dashboard', [
            'activeEvent' => $activeEvent,
            'currentGame' => $currentGame,
            'gameDuration' => $gameDuration,
            'finishedGames' => $finishedGames,
            'upcomingGames' => $upcomingGames,
            'activeLayout' => $this->activeLayout,
        ]);
    }

    public function switchLayout($layout)
    {
        $this->activeLayout = $layout;
    }
}
