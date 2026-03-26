<?php

namespace App\Livewire\GamePoints;

use App\Models\Game;
use App\Models\GamePoint;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AssignPoints extends Component
{
    /**
     * The game to assign points for.
     */
    public Game $game;

    /**
     * The selected player for each placement, keyed by placement number.
     *
     * @var array<int, int|string|null>
     */
    public array $selectedPlayers = [];

    /**
     * Initialize the component with the game.
     */
    public function mount(Game $game): void
    {
        $this->game = $game->load(['owners.user', 'points']);

        $existingPoints = $this->game->points
            ->whereBetween('placement', [1, $this->placementCount()])
            ->keyBy('placement');

        foreach (range(1, $this->placementCount()) as $placement) {
            $this->selectedPlayers[$placement] = $existingPoints->get($placement)?->player_id;
        }
    }

    /**
     * Save the configured placements for the game.
     */
    public function savePoints(): void
    {
        $this->validate();

        if (! $this->selectedPlayersAreUnique()) {
            return;
        }

        $now = now();
        $currentUser = Auth::id();
        $placementCount = $this->placementCount();

        DB::transaction(function () use ($currentUser, $now, $placementCount): void {
            $existingPoints = GamePoint::query()
                ->where('game_id', $this->game->id)
                ->get()
                ->keyBy('placement');

            foreach (range(1, $placementCount) as $placement) {
                $selectedPlayerId = $this->normalizedSelectedPlayer($placement);
                $existingPoint = $existingPoints->get($placement);

                if ($selectedPlayerId === null) {
                    if ($existingPoint !== null) {
                        $existingPoint->delete();
                    }

                    continue;
                }

                $points = $this->game->pointsForPlacement($placement);

                if ($existingPoint !== null) {
                    $existingPoint->update([
                        'player_id' => $selectedPlayerId,
                        'points' => $points,
                        'placement' => $placement,
                        'last_modified_by' => $currentUser,
                        'last_modified_at' => $now,
                    ]);

                    continue;
                }

                GamePoint::create([
                    'game_id' => $this->game->id,
                    'player_id' => $selectedPlayerId,
                    'points' => $points,
                    'placement' => $placement,
                    'assigned_by' => $currentUser,
                    'assigned_at' => $now,
                ]);
            }

            GamePoint::query()
                ->where('game_id', $this->game->id)
                ->where('placement', '>', $placementCount)
                ->delete();
        });

        $this->dispatch('points-saved');
        $this->dispatch('modal-close', name: 'assign-points-modal');
    }

    /**
     * Clear the current in-modal selections so placements can be reassigned from scratch.
     */
    public function resetSelections(): void
    {
        foreach (range(1, $this->placementCount()) as $placement) {
            $this->selectedPlayers[$placement] = null;
        }

        $this->resetValidation();
    }

    /**
     * Define validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (range(1, $this->placementCount()) as $placement) {
            $rules["selectedPlayers.{$placement}"] = [
                'nullable',
                'integer',
                Rule::in($this->availablePlayerIds()),
            ];
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
            'selectedPlayers.*.integer' => 'Please select a valid player.',
            'selectedPlayers.*.in' => 'Please select a player who is part of this game.',
        ];
    }

    /**
     * @return list<int>
     */
    public function availablePlayerIds(): array
    {
        return $this->game->owners
            ->pluck('user_id')
            ->map(static fn (mixed $playerId): int => (int) $playerId)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, nickname: ?string, display_name: string}>
     */
    public function availablePlayersForPlacement(int $placement): array
    {
        $selectedPlayerIds = collect($this->selectedPlayers)
            ->except($placement)
            ->filter(static fn (mixed $playerId): bool => filled($playerId))
            ->map(static fn (mixed $playerId): int => (int) $playerId)
            ->all();

        return $this->game->owners
            ->reject(static fn ($player): bool => in_array($player->user_id, $selectedPlayerIds, true))
            ->sortBy(fn ($player): string => mb_strtolower($player->display_name))
            ->map(static function ($player): array {
                return [
                    'id' => (int) $player->user_id,
                    'name' => $player->user->name,
                    'nickname' => $player->nickname,
                    'display_name' => $player->display_name,
                ];
            })
            ->values()
            ->all();
    }

    protected function selectedPlayersAreUnique(): bool
    {
        $duplicatePlayerIds = collect($this->selectedPlayers)
            ->filter(static fn (mixed $playerId): bool => filled($playerId))
            ->map(static fn (mixed $playerId): int => (int) $playerId)
            ->countBy()
            ->filter(static fn (int $count): bool => $count > 1)
            ->keys();

        if ($duplicatePlayerIds->isEmpty()) {
            return true;
        }

        foreach ($this->selectedPlayers as $placement => $playerId) {
            if ($playerId !== null && $playerId !== '' && $duplicatePlayerIds->contains((int) $playerId)) {
                $this->addError(
                    "selectedPlayers.{$placement}",
                    'Each player may only be assigned to one placement.',
                );
            }
        }

        return false;
    }

    protected function placementCount(): int
    {
        return count($this->game->pointsDistribution());
    }

    protected function normalizedSelectedPlayer(int $placement): ?int
    {
        $selectedPlayerId = $this->selectedPlayers[$placement] ?? null;

        if (! filled($selectedPlayerId)) {
            return null;
        }

        return (int) $selectedPlayerId;
    }

    public function render(): View
    {
        return view('livewire.game-points.assign-points', [
            'placements' => collect(range(1, $this->placementCount()))->map(function (int $placement): array {
                return [
                    'number' => $placement,
                    'points' => $this->game->pointsForPlacement($placement),
                    'players' => $this->availablePlayersForPlacement($placement),
                ];
            }),
        ]);
    }
}
