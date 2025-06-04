<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class ActionHistory extends Component
{
    /**
     * Maximum number of actions to store in history.
     */
    const MAX_HISTORY_SIZE = 10;

    /**
     * Collection of recent actions.
     */
    public Collection $actions;

    /**
     * Whether the history panel is expanded.
     */
    public bool $isExpanded = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->actions = collect();
    }

    /**
     * Record a game status change action.
     *
     * @param  Game  $game  The game that changed status
     * @param  GameStatus  $oldStatus  The previous status
     * @param  GameStatus  $newStatus  The new status
     */
    #[On('gameStatusChanged')]
    public function recordGameStatusChange(Game $game, GameStatus $oldStatus, GameStatus $newStatus): void
    {
        $this->addAction([
            'type' => 'status_change',
            'game_id' => $game->id,
            'game_name' => $game->name,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'timestamp' => now()->toIso8601String(),
            'message' => "Changed game \"{$game->name}\" status from {$oldStatus->label()} to {$newStatus->label()}",
            'can_undo' => true,
        ]);
    }

    /**
     * Record a player status change action.
     *
     * @param  Player  $player  The player that changed status
     * @param  Game  $game  The game the player is in
     * @param  bool  $isLeft  Whether the player left the game
     */
    #[On('playerStatusChanged')]
    public function recordPlayerStatusChange(Player $player, Game $game, bool $isLeft): void
    {
        $this->addAction([
            'type' => 'player_status_change',
            'player_id' => $player->id,
            'player_name' => $player->name,
            'game_id' => $game->id,
            'game_name' => $game->name,
            'is_left' => $isLeft,
            'timestamp' => now()->toIso8601String(),
            'message' => $isLeft
                ? "Marked player \"{$player->name}\" as left from game \"{$game->name}\""
                : "Marked player \"{$player->name}\" as active in game \"{$game->name}\"",
            'can_undo' => true,
        ]);
    }

    /**
     * Record a game reorder action.
     *
     * @param  Game  $game  The game that was reordered
     * @param  int  $oldOrder  The previous display order
     * @param  int  $newOrder  The new display order
     */
    #[On('gameReordered')]
    public function recordGameReorder(Game $game, int $oldOrder, int $newOrder): void
    {
        $this->addAction([
            'type' => 'game_reorder',
            'game_id' => $game->id,
            'game_name' => $game->name,
            'old_order' => $oldOrder,
            'new_order' => $newOrder,
            'timestamp' => now()->toIso8601String(),
            'message' => "Reordered game \"{$game->name}\" from position {$oldOrder} to {$newOrder}",
            'can_undo' => true,
        ]);
    }

    /**
     * Add an action to the history.
     *
     * @param  array  $action  The action to add
     */
    protected function addAction(array $action): void
    {
        // Add the action to the beginning of the collection
        $this->actions = $this->actions->prepend($action);

        // Limit the size of the history
        if ($this->actions->count() > self::MAX_HISTORY_SIZE) {
            $this->actions = $this->actions->take(self::MAX_HISTORY_SIZE);
        }
    }

    /**
     * Undo an action.
     *
     * @param  int  $index  The index of the action to undo
     */
    public function undoAction(int $index): void
    {
        if (! isset($this->actions[$index]) || ! $this->actions[$index]['can_undo']) {
            $this->dispatch('error', 'Cannot undo this action.');

            return;
        }

        $action = $this->actions[$index];

        try {
            switch ($action['type']) {
                case 'status_change':
                    $this->undoStatusChange($action);
                    break;
                case 'player_status_change':
                    $this->undoPlayerStatusChange($action);
                    break;
                case 'game_reorder':
                    $this->undoGameReorder($action);
                    break;
                default:
                    $this->dispatch('error', 'Unknown action type.');

                    return;
            }

            // Mark the action as undone
            $this->actions[$index]['can_undo'] = false;
            $this->actions[$index]['undone'] = true;
            $this->actions[$index]['message'] = 'UNDONE: '.$this->actions[$index]['message'];

            // Show success notification
            $this->dispatch('success', 'Action undone successfully.');

            // Notify the event runner that something changed
            $this->dispatch('gameStatusChanged');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to undo action: '.$e->getMessage());
        }
    }

    /**
     * Undo a game status change.
     *
     * @param  array  $action  The action to undo
     */
    protected function undoStatusChange(array $action): void
    {
        $game = Game::findOrFail($action['game_id']);
        $oldStatus = GameStatus::from($action['old_status']);

        // Revert the game status
        $game->status = $oldStatus;
        $game->save();
    }

    /**
     * Undo a player status change.
     *
     * @param  array  $action  The action to undo
     */
    protected function undoPlayerStatusChange(array $action): void
    {
        $player = Player::findOrFail($action['player_id']);
        $game = Game::findOrFail($action['game_id']);

        // Get the pivot relationship
        $pivot = $player->games()->where('game_id', $game->id)->first()->pivot;

        // Revert the left status
        $pivot->left = ! $action['is_left'];
        $pivot->save();
    }

    /**
     * Undo a game reorder.
     *
     * @param  array  $action  The action to undo
     */
    protected function undoGameReorder(array $action): void
    {
        $game = Game::findOrFail($action['game_id']);

        // Revert the display order
        $game->display_order = $action['old_order'];
        $game->save();
    }

    /**
     * Toggle the expanded state of the history panel.
     */
    public function toggleExpanded(): void
    {
        $this->isExpanded = ! $this->isExpanded;
    }

    /**
     * Clear all actions from the history.
     */
    public function clearHistory(): void
    {
        $this->actions = collect();
        $this->dispatch('success', 'Action history cleared.');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.games.action-history');
    }
}
