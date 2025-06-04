<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Models\Event;
use App\Models\Game;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuickStartNextGame extends Component
{
    /**
     * The current game that was just finished.
     */
    public Game $currentGame;

    /**
     * The next game to start, if available.
     */
    public ?Game $nextGame = null;

    /**
     * Whether the component is loading.
     */
    public bool $loading = false;

    /**
     * Mount the component with the current game.
     *
     * @param  Game  $game  The current game that was just finished
     */
    public function mount(Game $game): void
    {
        $this->currentGame = $game;
        $this->findNextGame();
    }

    /**
     * Find the next game that is ready to start.
     *
     * This method looks for the next game in the queue that is ready to start.
     */
    protected function findNextGame(): void
    {
        // Only show next game if current game is finished
        if ($this->currentGame->status !== GameStatus::Finished) {
            $this->nextGame = null;

            return;
        }

        // Get the event for the current game
        $event = $this->currentGame->event;

        // Find the next ready game with the lowest display_order
        $this->nextGame = $event->games()
            ->where('status', GameStatus::Ready)
            ->orderBy('display_order', 'asc')
            ->first();
    }

    /**
     * Start the next game.
     *
     * This method marks the next game as playing and redirects to the event runner.
     */
    public function startNextGame(): void
    {
        if (! $this->nextGame) {
            $this->dispatch('error', 'No next game available to start.');

            return;
        }

        $this->loading = true;

        try {
            // Mark the next game as playing
            $this->nextGame->status = GameStatus::Playing;
            $this->nextGame->save();

            // Notify the event runner that a game's status has changed
            $this->dispatch('gameStatusChanged');

            // Show success notification
            $this->dispatch('success', "Started playing {$this->nextGame->name}");

            // Reset loading state
            $this->loading = false;
        } catch (\Exception $e) {
            // Show error notification
            $this->dispatch('error', 'Failed to start the next game: '.$e->getMessage());
            $this->loading = false;
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.games.quick-start-next-game');
    }
}
