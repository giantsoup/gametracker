<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Models\Game;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GameStatusManager extends Component
{
    /**
     * The game being managed.
     */
    public Game $game;

    /**
     * Whether the component is in a loading state.
     */
    public bool $isLoading = false;

    /**
     * Whether to show the confirmation dialog.
     */
    public bool $showConfirmation = false;

    /**
     * The action to confirm.
     */
    public ?string $confirmAction = null;

    /**
     * Mount the component with the specified game.
     */
    public function mount(Game $game): void
    {
        $this->game = $game;
    }

    /**
     * Get the current status of the game.
     */
    #[Computed]
    public function status(): ?GameStatus
    {
        return $this->game->status;
    }

    /**
     * Get the next status in the progression.
     */
    #[Computed]
    public function nextStatus(): ?GameStatus
    {
        return $this->status?->getNextStatus();
    }

    /**
     * Check if the game can transition to the given status.
     */
    public function canTransitionTo(GameStatus $status): bool
    {
        return $this->status === null || $this->status->canTransitionTo($status);
    }

    /**
     * Show the confirmation dialog for the given action.
     */
    public function confirmAction(string $action): void
    {
        $this->confirmAction = $action;
        $this->showConfirmation = true;
    }

    /**
     * Cancel the confirmation dialog.
     */
    public function cancelConfirmation(): void
    {
        $this->showConfirmation = false;
        $this->confirmAction = null;
    }

    /**
     * Execute the confirmed action.
     */
    public function executeConfirmedAction(): void
    {
        if ($this->confirmAction === 'markAsReady') {
            $this->markAsReady();
        } elseif ($this->confirmAction === 'markAsPlaying') {
            $this->markAsPlaying();
        } elseif ($this->confirmAction === 'markAsFinished') {
            $this->markAsFinished();
        } elseif ($this->confirmAction === 'markAsBackground') {
            $this->markAsBackground();
        }

        $this->showConfirmation = false;
        $this->confirmAction = null;
    }

    /**
     * Mark the game as ready to start.
     */
    public function markAsReady(): void
    {
        $this->isLoading = true;

        // Store the old status before changing
        $oldStatus = $this->game->status;

        $success = $this->game->markAsReady();

        if ($success) {
            // Dispatch event with both old and new status for action history
            $this->dispatch('gameStatusChanged',
                game: $this->game,
                oldStatus: $oldStatus,
                newStatus: GameStatus::Ready
            );

            // Also dispatch the original event for backward compatibility
            $this->dispatch('gameStatusChanged', gameId: $this->game->id, status: GameStatus::Ready->value);
        }

        $this->isLoading = false;
    }

    /**
     * Mark the game as currently playing.
     */
    public function markAsPlaying(): void
    {
        $this->isLoading = true;

        // Store the old status before changing
        $oldStatus = $this->game->status;

        $success = $this->game->markAsPlaying();

        if ($success) {
            // Dispatch event with both old and new status for action history
            $this->dispatch('gameStatusChanged',
                game: $this->game,
                oldStatus: $oldStatus,
                newStatus: GameStatus::Playing
            );

            // Also dispatch the original event for backward compatibility
            $this->dispatch('gameStatusChanged', gameId: $this->game->id, status: GameStatus::Playing->value);
        }

        $this->isLoading = false;
    }

    /**
     * Mark the game as finished.
     */
    public function markAsFinished(): void
    {
        $this->isLoading = true;

        // Store the old status before changing
        $oldStatus = $this->game->status;

        $success = $this->game->markAsFinished();

        if ($success) {
            // Dispatch event with both old and new status for action history
            $this->dispatch('gameStatusChanged',
                game: $this->game,
                oldStatus: $oldStatus,
                newStatus: GameStatus::Finished
            );

            // Also dispatch the original event for backward compatibility
            $this->dispatch('gameStatusChanged', gameId: $this->game->id, status: GameStatus::Finished->value);
        }

        $this->isLoading = false;
    }

    /**
     * Mark the game as a background game.
     */
    public function markAsBackground(): void
    {
        $this->isLoading = true;

        // Store the old status before changing
        $oldStatus = $this->game->status;

        $success = $this->game->markAsBackground();

        if ($success) {
            // Dispatch event with both old and new status for action history
            $this->dispatch('gameStatusChanged',
                game: $this->game,
                oldStatus: $oldStatus,
                newStatus: GameStatus::Background
            );

            // Also dispatch the original event for backward compatibility
            $this->dispatch('gameStatusChanged', gameId: $this->game->id, status: GameStatus::Background->value);
        }

        $this->isLoading = false;
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.games.game-status-manager');
    }
}
