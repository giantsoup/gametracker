<?php

namespace App\Livewire;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Contracts\View\View;

class EventRunnerVercel extends EventRunner
{
    /**
     * Form properties for adding a new game.
     */
    public string $gameTitle = '';

    public int $gameDuration = 60;

    public string $gameOwner = '';

    public int $maxPlayers = 4;

    public string $gameStatus = 'ready';

    /**
     * Flag to control the visibility of the add game modal.
     */
    public bool $showAddGameModal = false;

    /**
     * Properties for the manage players modal.
     */
    public bool $showManagePlayersModal = false;

    public ?Game $selectedGame = null;

    public array $availablePlayers = [];

    /**
     * Listen for the open-add-game-modal event to show the modal.
     */
    #[\Livewire\Attributes\On('open-add-game-modal')]
    public function openAddGameModal(): void
    {
        $this->showAddGameModal = true;
    }

    /**
     * Close the add game modal.
     */
    public function closeAddGameModal(): void
    {
        $this->showAddGameModal = false;
    }

    /**
     * Add a new game to the event.
     *
     * This method creates a new game with the provided details and adds it to the current event.
     */
    public function addGame(): void
    {
        // Validate the form data
        $this->validate([
            'gameTitle' => 'required|string|min:3|max:255',
            'gameDuration' => 'required|integer|min:15|max:480',
            'gameOwner' => 'required|string|max:255',
            'maxPlayers' => 'required|integer|min:1|max:12',
            'gameStatus' => 'required|in:ready,background',
        ]);

        // Create a new game
        $game = new Game([
            'name' => $this->gameTitle,
            'duration' => $this->gameDuration,
            'game_master' => $this->gameOwner,
            'max_players' => $this->maxPlayers,
            'event_id' => $this->event->id,
            'status' => $this->gameStatus === 'ready' ? GameStatus::Ready : GameStatus::Background,
            'display_order' => Game::where('event_id', $this->event->id)
                ->where('status', GameStatus::Ready)
                ->count() + 1,
        ]);

        $game->save();

        // Reset the form
        $this->reset(['gameTitle', 'gameDuration', 'gameOwner', 'maxPlayers', 'gameStatus']);

        // Close the modal
        $this->closeAddGameModal();

        // Show success notification
        $this->dispatch('success', "Added new game: {$game->name}");
    }

    /**
     * Start a game.
     *
     * This method changes a game's status from Ready to Playing.
     */
    public function startGame(Game $game): void
    {
        // Only allow starting Ready games
        if ($game->status !== GameStatus::Ready) {
            return;
        }

        // Store original status for undo functionality
        $oldStatus = $game->status;

        // Update the game status
        $game->status = GameStatus::Playing;
        $game->started_at = now();
        $game->save();

        // Dispatch event for action history
        $this->dispatch('gameStatusChanged',
            game: $game,
            oldStatus: $oldStatus,
            newStatus: $game->status
        );

        // Show success notification
        $this->dispatch('success', "Started {$game->name}");
    }

    /**
     * Finish a game.
     *
     * This method changes a game's status to Finished.
     */
    public function finishGame(Game $game): void
    {
        // Only allow finishing Playing or Background games
        if ($game->status !== GameStatus::Playing && $game->status !== GameStatus::Background) {
            return;
        }

        // Store original status for undo functionality
        $oldStatus = $game->status;

        // Update the game status
        $game->status = GameStatus::Finished;
        $game->finished_at = now();
        $game->save();

        // Dispatch event for action history
        $this->dispatch('gameStatusChanged',
            game: $game,
            oldStatus: $oldStatus,
            newStatus: $game->status
        );

        // Show success notification
        $this->dispatch('success', "Finished {$game->name}");
    }

    /**
     * Open the manage players modal for a specific game.
     *
     * This method prepares the data needed for the manage players modal.
     */
    public function openManagePlayersModal(int $gameId): void
    {
        $this->selectedGame = Game::with('players')->find($gameId);

        if (! $this->selectedGame) {
            return;
        }

        // Get all players in the event who aren't already in this game
        $players = Player::where('event_id', $this->event->id)
            ->whereNotIn('id', $this->selectedGame->players->pluck('id'))
            ->get();

        // Convert to array and ensure name key exists
        $this->availablePlayers = [];
        foreach ($players as $player) {
            $playerArray = $player->toArray();
            // Ensure the name key exists
            if (! isset($playerArray['name']) && isset($player->name)) {
                $playerArray['name'] = $player->name;
            }
            $this->availablePlayers[] = $playerArray;
        }

        $this->showManagePlayersModal = true;
    }

    /**
     * Close the manage players modal.
     */
    public function closeManagePlayersModal(): void
    {
        $this->showManagePlayersModal = false;
        $this->selectedGame = null;
        $this->availablePlayers = [];
    }

    /**
     * Add a player to the selected game.
     */
    public function addPlayerToGame(int $playerId): void
    {
        if (! $this->selectedGame) {
            return;
        }

        $player = Player::find($playerId);

        if (! $player) {
            return;
        }

        // Check if the game is at max capacity
        if ($this->selectedGame->players->count() >= ($this->selectedGame->max_players ?? 8)) {
            $this->dispatch('error', 'Game is at maximum capacity');

            return;
        }

        // Add the player to the game
        $this->selectedGame->players()->attach($player->id);

        // Refresh the selected game
        $this->selectedGame = Game::with('players')->find($this->selectedGame->id);

        // Remove the player from available players
        $this->availablePlayers = array_filter($this->availablePlayers, function ($p) use ($playerId) {
            return $p['id'] !== $playerId;
        });

        // Dispatch event for action history
        $this->dispatch('playerStatusChanged',
            player: $player,
            game: $this->selectedGame,
            isLeft: false
        );

        // Show success notification
        $this->dispatch('success', "Added {$player->name} to {$this->selectedGame->name}");
    }

    /**
     * Remove a player from the selected game.
     */
    public function removePlayerFromGame(int $playerId): void
    {
        if (! $this->selectedGame) {
            return;
        }

        $player = Player::find($playerId);

        if (! $player) {
            return;
        }

        // Remove the player from the game
        $this->selectedGame->players()->detach($player->id);

        // Refresh the selected game
        $this->selectedGame = Game::with('players')->find($this->selectedGame->id);

        // Add the player back to available players
        $playerArray = $player->toArray();
        // Ensure the name key exists
        if (! isset($playerArray['name']) && isset($player->name)) {
            $playerArray['name'] = $player->name;
        }
        $this->availablePlayers[] = $playerArray;

        // Dispatch event for action history
        $this->dispatch('playerStatusChanged',
            player: $player,
            game: $this->selectedGame,
            isLeft: true
        );

        // Show success notification
        $this->dispatch('success', "Removed {$player->name} from {$this->selectedGame->name}");
    }

    /**
     * Render the component.
     *
     * This method returns the view that will be rendered by the component.
     */
    public function render(): View
    {
        return view('livewire.event-runner-vercel', [
            'event' => $this->event,
            'currentlyPlayingGames' => $this->getCurrentlyPlayingGames(),
            'readyToStartGames' => $this->getReadyToStartGames(),
            'finishedGames' => $this->getFinishedGames(),
            'backgroundGames' => $this->getBackgroundGames(),
            'groupedEvents' => $this->getGroupedEvents(),
        ]);
    }
}
