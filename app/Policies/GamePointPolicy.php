<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\GamePoint;
use App\Models\User;

class GamePointPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Admins can do everything
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Fall through to the specific policy methods
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view game points
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GamePoint $gamePoint): bool
    {
        // All authenticated users can view game points
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Game $game): bool
    {
        // Game owners can create points for their games
        return $this->isGameOwner($user, $game);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GamePoint $gamePoint): bool
    {
        // Game owners can only update points if the event hasn't ended
        if ($this->isGameOwner($user, $gamePoint->game)) {
            return ! $gamePoint->game->event->hasEnded();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GamePoint $gamePoint): bool
    {
        // Only admins can delete points (handled by before method)
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GamePoint $gamePoint): bool
    {
        // Only admins can restore points (handled by before method)
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GamePoint $gamePoint): bool
    {
        // Only admins can force delete points (handled by before method)
        return false;
    }

    /**
     * Check if the user is an owner of the game.
     */
    private function isGameOwner(User $user, Game $game): bool
    {
        return $game->owners()
            ->whereHas('user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->exists();
    }
}
