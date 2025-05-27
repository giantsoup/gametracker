<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GamePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any games.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view games list
    }

    /**
     * Determine whether the user can view the game.
     */
    public function view(User $user, Game $game): bool
    {
        return true; // All authenticated users can view individual games
    }

    /**
     * Determine whether the user can create games.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create games
    }

    /**
     * Determine whether the user can update the game.
     */
    public function update(User $user, Game $game): bool
    {
        // Only allow updates if the event hasn't ended
        return ! $game->event->hasEnded();
    }

    /**
     * Determine whether the user can delete the game.
     */
    public function delete(User $user, Game $game): bool
    {
        // Only allow deletion if the event hasn't started yet
        return ! $game->event->hasStarted();
    }

    /**
     * Determine whether the user can restore the game.
     */
    public function restore(User $user, Game $game): bool
    {
        return true; // All authenticated users can restore games
    }

    /**
     * Determine whether the user can permanently delete the game.
     */
    public function forceDelete(User $user, Game $game): bool
    {
        return false; // No users can permanently delete games
    }
}
