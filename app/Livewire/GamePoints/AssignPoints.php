<?php

namespace App\Livewire\GamePoints;

use App\Models\Game;
use App\Models\GamePoint;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssignPoints extends Component
{
    /**
     * The game to assign points for.
     */
    public Game $game;

    /**
     * Array of player points, keyed by player ID.
     *
     * @var array<int, int>
     */
    public array $playerPoints = [];

    /**
     * Array of player placements, keyed by player ID.
     *
     * @var array<int, int|null>
     */
    public array $placements = [];

    /**
     * Initialize the component with the game.
     */
    public function mount(Game $game): void
    {
        $this->game = $game;

        // Initialize playerPoints and placements arrays with existing data if available
        foreach ($this->game->owners as $player) {
            $gamePoint = GamePoint::where('game_id', $this->game->id)
                ->where('player_id', $player->user_id)
                ->first();

            if ($gamePoint) {
                $this->playerPoints[$player->user_id] = $gamePoint->points;
                $this->placements[$player->user_id] = $gamePoint->placement;
            } else {
                $this->playerPoints[$player->user_id] = 0;
                $this->placements[$player->user_id] = null;
            }
        }
    }

    /**
     * Save the points for all players in the game.
     */
    public function savePoints(): void
    {
        $this->validate();

        $now = now();
        $currentUser = Auth::id();

        foreach ($this->playerPoints as $playerId => $points) {
            // Skip if points are 0 and no placement
            if ($points == 0 && empty($this->placements[$playerId])) {
                continue;
            }

            // Check if a record already exists
            $gamePoint = GamePoint::where('game_id', $this->game->id)
                ->where('player_id', $playerId)
                ->first();

            if ($gamePoint) {
                // Update existing record
                $gamePoint->update([
                    'points' => $points,
                    'placement' => $this->placements[$playerId] ?? null,
                    'last_modified_by' => $currentUser,
                    'last_modified_at' => $now,
                ]);
            } else {
                // Create new record
                GamePoint::create([
                    'game_id' => $this->game->id,
                    'player_id' => $playerId,
                    'points' => $points,
                    'placement' => $this->placements[$playerId] ?? null,
                    'assigned_by' => $currentUser,
                    'assigned_at' => $now,
                ]);
            }
        }

        $this->dispatch('points-saved');
    }

    /**
     * Define validation rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (array_keys($this->playerPoints) as $playerId) {
            $rules["playerPoints.{$playerId}"] = 'integer|min:0';
            $rules["placements.{$playerId}"] = 'nullable|integer|min:1';
        }

        return $rules;
    }

    /**
     * Define custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'playerPoints.*.integer' => 'Points must be a whole number.',
            'playerPoints.*.min' => 'Points cannot be negative.',
            'placements.*.integer' => 'Placement must be a whole number.',
            'placements.*.min' => 'Placement must be at least 1.',
        ];
    }

    /**
     * Automatically calculate points based on placement.
     */
    public function calculatePointsFromPlacement(int $playerId): void
    {
        $placement = $this->placements[$playerId] ?? null;

        if ($placement === null) {
            return;
        }

        // Apply points based on placement according to business rules
        switch ($placement) {
            case 1:
                $this->playerPoints[$playerId] = 5;
                break;
            case 2:
                $this->playerPoints[$playerId] = 3;
                break;
            case 3:
                $this->playerPoints[$playerId] = 1;
                break;
            default:
                $this->playerPoints[$playerId] = 0;
                break;
        }
    }

    public function render()
    {
        return view('livewire.game-points.assign-points', [
            'players' => $this->game->owners->map(function ($player) {
                return [
                    'id' => $player->user_id,
                    'name' => $player->user->name,
                    'nickname' => $player->nickname,
                ];
            }),
        ]);
    }
}
