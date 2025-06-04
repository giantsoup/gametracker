<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class GameSummaryStatistics extends Component
{
    /**
     * The event to show statistics for.
     */
    public Event $event;

    /**
     * Whether to show the statistics modal.
     */
    public bool $showModal = false;

    /**
     * The collection of player statistics.
     */
    public Collection $playerStats;

    /**
     * The collection of game statistics.
     */
    public Collection $gameStats;

    /**
     * Mount the component with the event.
     *
     * @param  Event  $event  The event to show statistics for
     */
    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->playerStats = collect();
        $this->gameStats = collect();
    }

    /**
     * Show the statistics modal.
     *
     * This method calculates all the statistics and shows the modal.
     */
    public function showStatistics(): void
    {
        $this->calculateStatistics();
        $this->showModal = true;
    }

    /**
     * Hide the statistics modal.
     */
    public function hideStatistics(): void
    {
        $this->showModal = false;
    }

    /**
     * Calculate all statistics for the event.
     *
     * This method calculates player and game statistics for the event.
     */
    protected function calculateStatistics(): void
    {
        // Get all finished games for this event
        $finishedGames = $this->event->games()
            ->where('status', GameStatus::Finished)
            ->with(['players', 'gamePoints.player'])
            ->get();

        // Calculate game statistics
        $this->calculateGameStatistics($finishedGames);

        // Calculate player statistics
        $this->calculatePlayerStatistics($finishedGames);
    }

    /**
     * Calculate game statistics.
     *
     * @param  Collection  $finishedGames  The collection of finished games
     */
    protected function calculateGameStatistics(Collection $finishedGames): void
    {
        $this->gameStats = $finishedGames->map(function (Game $game) {
            $duration = $game->actual_duration ?? $game->duration;
            $playerCount = $game->players->count();
            $winnerName = null;
            $highestScore = 0;

            // Find the winner (player with highest points)
            $gamePoints = $game->gamePoints;
            if ($gamePoints->isNotEmpty()) {
                $highestPoints = $gamePoints->sortByDesc('points')->first();
                if ($highestPoints) {
                    $winnerName = $highestPoints->player->name;
                    $highestScore = $highestPoints->points;
                }
            }

            return [
                'id' => $game->id,
                'name' => $game->name,
                'duration' => $duration,
                'playerCount' => $playerCount,
                'winnerName' => $winnerName,
                'highestScore' => $highestScore,
                'date' => $game->updated_at->format('M d, Y'),
            ];
        });
    }

    /**
     * Calculate player statistics.
     *
     * @param  Collection  $finishedGames  The collection of finished games
     */
    protected function calculatePlayerStatistics(Collection $finishedGames): void
    {
        // Get all players in the event
        $players = $this->event->players;

        // Initialize player stats
        $playerStatsMap = [];
        foreach ($players as $player) {
            $playerStatsMap[$player->id] = [
                'id' => $player->id,
                'name' => $player->name,
                'gamesPlayed' => 0,
                'wins' => 0,
                'totalPoints' => 0,
                'averagePoints' => 0,
                'bestGame' => null,
                'bestScore' => 0,
            ];
        }

        // Calculate statistics for each game
        foreach ($finishedGames as $game) {
            $gamePoints = $game->gamePoints;

            // Skip games without points
            if ($gamePoints->isEmpty()) {
                continue;
            }

            // Find the winner (player with placement = 1)
            $winner = $gamePoints->firstWhere('placement', 1);

            // Update player statistics
            foreach ($gamePoints as $point) {
                $playerId = $point->player_id;

                // Skip if player is not in the event (shouldn't happen, but just in case)
                if (! isset($playerStatsMap[$playerId])) {
                    continue;
                }

                // Update games played
                $playerStatsMap[$playerId]['gamesPlayed']++;

                // Update wins
                if ($winner && $winner->player_id === $playerId) {
                    $playerStatsMap[$playerId]['wins']++;
                }

                // Update total points
                $playerStatsMap[$playerId]['totalPoints'] += $point->points;

                // Update best game and score
                if ($point->points > $playerStatsMap[$playerId]['bestScore']) {
                    $playerStatsMap[$playerId]['bestScore'] = $point->points;
                    $playerStatsMap[$playerId]['bestGame'] = $game->name;
                }
            }
        }

        // Calculate average points and convert to collection
        $this->playerStats = collect(array_values($playerStatsMap))
            ->map(function ($stats) {
                if ($stats['gamesPlayed'] > 0) {
                    $stats['averagePoints'] = round($stats['totalPoints'] / $stats['gamesPlayed'], 1);
                }

                return $stats;
            })
            ->sortByDesc('totalPoints');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.games.game-summary-statistics');
    }
}
