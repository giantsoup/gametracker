<?php

namespace App\Livewire\GamePoints;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlacementWizard extends Component
{
    /**
     * The game to assign points for.
     */
    public Game $game;

    /**
     * The current step in the wizard.
     */
    public int $currentStep = 1;

    /**
     * The total number of steps in the wizard.
     */
    public int $totalSteps = 1;

    /**
     * The selected player IDs in order of placement.
     *
     * @var array<int, int>
     */
    public array $selectedPlayers = [];

    /**
     * The calculated points for each player.
     *
     * @var array<int, int>
     */
    public array $playerPoints = [];

    /**
     * Whether to show the confirmation dialog.
     */
    public bool $showConfirmation = false;

    /**
     * Whether the points have been saved.
     */
    public bool $pointsSaved = false;

    /**
     * Initialize the component with the game.
     */
    public function mount(Game $game): void
    {
        $this->game = $game;

        // Ensure the game is in Finished status
        if ($this->game->status !== GameStatus::Finished) {
            session()->flash('error', 'Points can only be assigned to finished games.');

            return;
        }

        // Get active players (not left)
        $activePlayers = $this->getActivePlayers();

        // Set the total steps (one per player plus confirmation)
        $this->totalSteps = $activePlayers->count() + 1;

        // Initialize the selected players array
        $this->selectedPlayers = array_fill(1, $activePlayers->count(), 0);

        // Initialize the player points array
        $this->playerPoints = [];
    }

    /**
     * Get active players in the game (not left).
     */
    public function getActivePlayers(): Collection
    {
        return $this->game->activePlayers()->with('user')->get();
    }

    /**
     * Get the remaining players who haven't been assigned a placement.
     */
    public function getRemainingPlayers(): Collection
    {
        $activePlayers = $this->getActivePlayers();

        // Filter out players who have already been selected
        return $activePlayers->filter(function (Player $player) {
            return ! in_array($player->id, $this->selectedPlayers);
        });
    }

    /**
     * Get the players who have been assigned a placement, in order of placement.
     */
    public function getSelectedPlayers(): Collection
    {
        $activePlayers = $this->getActivePlayers();
        $selectedPlayers = collect();

        foreach ($this->selectedPlayers as $placement => $playerId) {
            if ($playerId > 0) {
                $player = $activePlayers->firstWhere('id', $playerId);
                if ($player) {
                    $selectedPlayers->put($placement, $player);
                }
            }
        }

        return $selectedPlayers;
    }

    /**
     * Select a player for the current placement.
     */
    public function selectPlayer(int $playerId): void
    {
        // Assign the player to the current placement
        $this->selectedPlayers[$this->currentStep] = $playerId;

        // Calculate points for this placement
        $this->calculatePoints($this->currentStep, $playerId);

        // Move to the next step
        $this->nextStep();
    }

    /**
     * Calculate points for a player based on placement.
     */
    public function calculatePoints(int $placement, int $playerId): void
    {
        // Get the player's user ID
        $player = $this->getActivePlayers()->firstWhere('id', $playerId);
        if (! $player) {
            return;
        }

        // Calculate points based on placement
        $points = $this->game->getPointsForPlacement($placement);

        // Store the points
        $this->playerPoints[$player->user_id] = $points;
    }

    /**
     * Move to the next step.
     */
    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    /**
     * Move to the previous step.
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;

            // Clear the selection for the current step
            if (isset($this->selectedPlayers[$this->currentStep])) {
                $playerId = $this->selectedPlayers[$this->currentStep];
                $player = $this->getActivePlayers()->firstWhere('id', $playerId);
                if ($player) {
                    unset($this->playerPoints[$player->user_id]);
                }
                $this->selectedPlayers[$this->currentStep] = 0;
            }
        }
    }

    /**
     * Show the confirmation dialog.
     */
    public function showConfirmation(): void
    {
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
     * Save the points for all players.
     */
    public function savePoints(): void
    {
        $now = now();
        $currentUser = Auth::id();

        foreach ($this->playerPoints as $playerId => $points) {
            // Skip if points are 0
            if ($points == 0) {
                continue;
            }

            // Get the placement for this player
            $placement = array_search(
                $this->getActivePlayers()->firstWhere('user_id', $playerId)->id,
                $this->selectedPlayers
            );

            // Check if a record already exists
            $gamePoint = GamePoint::where('game_id', $this->game->id)
                ->where('player_id', $playerId)
                ->first();

            if ($gamePoint) {
                // Update existing record
                $gamePoint->update([
                    'points' => $points,
                    'placement' => $placement,
                    'last_modified_by' => $currentUser,
                    'last_modified_at' => $now,
                ]);
            } else {
                // Create new record
                GamePoint::create([
                    'game_id' => $this->game->id,
                    'player_id' => $playerId,
                    'points' => $points,
                    'placement' => $placement,
                    'assigned_by' => $currentUser,
                    'assigned_at' => $now,
                ]);
            }
        }

        $this->pointsSaved = true;
        $this->showConfirmation = false;

        // Dispatch an event to notify other components that points have been saved
        $this->dispatch('points-saved');

        // Redirect to the event runner with the event ID as a query parameter
        $this->redirect(route('event-runner.show', ['eventId' => $this->game->event_id]));
    }

    /**
     * Get the total points assigned.
     */
    public function getTotalPoints(): int
    {
        return array_sum($this->playerPoints);
    }

    /**
     * Convert a number to its ordinal representation (1st, 2nd, 3rd, etc.).
     */
    public function getOrdinal(int $number): string
    {
        $suffix = match ($number % 100) {
            11, 12, 13 => 'th',
            default => match ($number % 10) {
                1 => 'st',
                2 => 'nd',
                3 => 'rd',
                default => 'th',
            },
        };

        return $number.$suffix;
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.game-points.placement-wizard', [
            'activePlayers' => $this->getActivePlayers(),
            'remainingPlayers' => $this->getRemainingPlayers(),
            'selectedPlayers' => $this->getSelectedPlayers(),
            'totalPoints' => $this->getTotalPoints(),
        ]);
    }
}
