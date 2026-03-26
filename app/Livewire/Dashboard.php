<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $activeEvent = Event::active()->first();

        $currentGame = null;
        $gameDuration = null;
        $finishedGames = collect();
        $upcomingGames = collect();

        if ($activeEvent) {
            $currentGame = $activeEvent->games()->latest()->first();

            if ($currentGame && $activeEvent->started_at) {
                $gameDuration = now()->diffForHumans($activeEvent->started_at, true);
            }

            $finishedGames = $activeEvent->games()
                ->when($currentGame, function ($query) use ($currentGame) {
                    return $query->where('id', '!=', $currentGame->id);
                })
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

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
        ]);
    }
}
