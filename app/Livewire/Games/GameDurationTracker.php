<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GameDurationTracker extends Component
{
    /**
     * The game being tracked.
     */
    public Game $game;

    /**
     * The current duration in minutes.
     */
    public int $currentDuration = 0;

    /**
     * Whether the game is over the estimated duration.
     */
    public bool $isOvertime = false;

    /**
     * The percentage of the estimated duration that has been used.
     */
    public int $percentComplete = 0;

    /**
     * The formatted start time.
     */
    public ?string $startTime = null;

    /**
     * The estimated end time.
     */
    public ?string $estimatedEndTime = null;

    /**
     * Mount the component with the game.
     *
     * @param  Game  $game  The game to track
     */
    public function mount(Game $game): void
    {
        $this->game = $game;
        $this->updateDurationInfo();
    }

    /**
     * Update the duration information.
     *
     * This method calculates the current duration, percentage complete,
     * and whether the game is in overtime.
     */
    public function updateDurationInfo(): void
    {
        // Only track duration for playing games
        if ($this->game->status !== GameStatus::Playing) {
            return;
        }

        // Calculate current duration
        $this->currentDuration = $this->game->getCurrentSessionDuration();

        // Calculate percentage complete
        $estimatedDuration = max(1, $this->game->duration); // Avoid division by zero
        $this->percentComplete = min(100, round(($this->currentDuration / $estimatedDuration) * 100));

        // Check if game is overtime
        $this->isOvertime = $this->currentDuration > $this->game->duration;

        // Format start time if available
        if ($this->game->current_session_started_at) {
            $startTime = Carbon::parse($this->game->current_session_started_at);
            $this->startTime = $startTime->format('g:i A');

            // Calculate estimated end time
            $estimatedEndTime = $startTime->copy()->addMinutes($this->game->duration);
            $this->estimatedEndTime = $estimatedEndTime->format('g:i A');
        }
    }

    /**
     * Refresh the duration information.
     *
     * This method is called by a polling component to update the duration in real-time.
     */
    public function refresh(): void
    {
        $this->updateDurationInfo();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.games.game-duration-tracker');
    }
}
