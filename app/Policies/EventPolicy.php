<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view events list
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        return true; // All authenticated users can view individual events
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        // Customize this based on your requirements - e.g., only admins
        return true;
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        // Only allow updates if the event hasn't ended
        return ! $event->hasEnded();
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        // Only allow deletion if the event hasn't started yet
        return ! $event->hasStarted();
    }

    /**
     * Determine whether the user can restore the event.
     */
    public function restore(User $user, Event $event): bool
    {
        // Customize based on requirements, for example only admins
        return true;
    }

    /**
     * Determine whether the user can permanently delete the event.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        // Typically limit permanent deletion to admins or similar
        return false;
    }

    /**
     * Determine whether the user can start the event.
     */
    public function start(User $user, Event $event): bool
    {
        return $event->isActive() &&
            ! $event->hasStarted() &&
            now()->gte($event->starts_at);
    }

    /**
     * Determine whether the user can end the event.
     */
    public function end(User $user, Event $event): bool
    {
        return $event->isActive() &&
            $event->hasStarted() &&
            ! $event->hasEnded() &&
            (now()->gte($event->ends_at) || $event->ends_at === null);
    }
}
