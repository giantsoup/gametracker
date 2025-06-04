<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Models\Game;
use Livewire\Component;

class GameTimer extends Component
{
    public Game $game;

    public function mount(Game $game)
    {
        $this->game = $game;
    }

    public function startGame()
    {
        $this->game->startGame();
        $this->dispatch('game-started');
    }

    public function stopGame()
    {
        $this->game->stopGame();
        $this->dispatch('game-stopped');
    }

    public function setStatusUnplayed()
    {
        $this->game->setStatus(GameStatus::Unplayed);
        $this->dispatch('game-status-updated');
    }

    public function setStatusActive()
    {
        $this->game->setStatus(GameStatus::Active);
        $this->dispatch('game-status-updated');
    }

    public function setStatusPlayed()
    {
        $this->game->setStatus(GameStatus::Played);
        $this->dispatch('game-status-updated');
    }

    #[Polling('5s')]
    public function render()
    {
        // Only poll if the game is running
        if (! $this->game->isRunning()) {
            $this->skipPoll();
        }

        return view('livewire.games.game-timer', [
            'isRunning' => $this->game->isRunning(),
            'accumulatedDuration' => $this->game->accumulated_duration,
            'currentSessionDuration' => $this->game->getCurrentSessionDuration(),
            'totalDuration' => $this->game->getTotalDuration(),
            'totalDurationForHumans' => $this->game->getTotalDurationForHumans(),
            'status' => $this->game->status,
            'statusLabel' => $this->game->getStatusLabel(),
            'statusColor' => $this->game->getStatusColor(),
        ]);
    }
}
