<?php

namespace App\Livewire\Games;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class GamePlayersList extends Component
{
    /**
     * The game whose players are being displayed.
     */
    public Game $game;

    /**
     * Whether the component is in manage mode.
     */
    public bool $manageMode = false;

    /**
     * The IDs of selected players.
     *
     * @var array<int>
     */
    public array $selectedPlayers = [];

    /**
     * Whether to show the confirmation modal.
     */
    public bool $showConfirmation = false;

    /**
     * Mount the component with the specified game.
     */
    public function mount(Game $game): void
    {
        $this->game = $game;
    }

    /**
     * Toggle the manage mode.
     */
    public function toggleManageMode(): void
    {
        $this->manageMode = ! $this->manageMode;
        $this->selectedPlayers = [];
    }

    /**
     * Toggle the selection of a player.
     */
    public function togglePlayerSelection(int $playerId): void
    {
        if (in_array($playerId, $this->selectedPlayers)) {
            $this->selectedPlayers = array_diff($this->selectedPlayers, [$playerId]);
        } else {
            $this->selectedPlayers[] = $playerId;
        }
    }

    /**
     * Show the confirmation modal for marking players as left.
     */
    public function confirmMarkAsLeft(): void
    {
        if (empty($this->selectedPlayers)) {
            return;
        }

        $this->showConfirmation = true;
    }

    /**
     * Cancel the confirmation.
     */
    public function cancelConfirmation(): void
    {
        $this->showConfirmation = false;
    }

    /**
     * Mark the selected players as having left the game.
     */
    public function markPlayersAsLeft(): void
    {
        if (empty($this->selectedPlayers)) {
            return;
        }

        $players = Player::whereIn('id', $this->selectedPlayers)->get();

        foreach ($players as $player) {
            $this->game->markPlayerLeft($player);

            // Dispatch event for action history
            $this->dispatch('playerStatusChanged',
                player: $player,
                game: $this->game,
                isLeft: true
            );
        }

        $this->selectedPlayers = [];
        $this->showConfirmation = false;

        // Dispatch an event to notify other components that players have been updated
        $this->dispatch('players-updated', gameId: $this->game->id);

        // Show success notification
        $this->dispatch('success', count($players).' player(s) marked as left');
    }

    /**
     * Get all players in the game, including those who have left.
     */
    public function getAllPlayers(): Collection
    {
        return $this->game->players()->with('user')->get();
    }

    /**
     * Get active players in the game (not left).
     */
    public function getActivePlayers(): Collection
    {
        return $this->game->activePlayers()->with('user')->get();
    }

    /**
     * Get players who have left the game.
     */
    public function getLeftPlayers(): Collection
    {
        return $this->game->players()
            ->wherePivotNotNull('left_at')
            ->with('user')
            ->get();
    }

    /**
     * Check if a player is an owner of the game.
     */
    public function isOwner(Player $player): bool
    {
        return $this->game->owners->contains($player->id);
    }

    /**
     * Refresh the component when players are updated.
     */
    #[On('players-updated')]
    public function handlePlayersUpdated(int $gameId): void
    {
        if ($gameId === $this->game->id) {
            // The component will automatically re-render
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.games.game-players-list', [
            'allPlayers' => $this->getAllPlayers(),
            'activePlayers' => $this->getActivePlayers(),
            'leftPlayers' => $this->getLeftPlayers(),
        ]);
    }
}
