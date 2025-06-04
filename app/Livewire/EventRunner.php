<?php

namespace App\Livewire;

use App\Enums\GameStatus;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class EventRunner extends Component
{
    /**
     * The event being managed by the Event Runner.
     */
    public Event $event;

    /**
     * The ID of the selected event.
     */
    public ?int $selectedEventId = null;

    /**
     * Mount the component and initialize with the specified event ID from the query parameters,
     * or the first active event, or the first event if no active events exist.
     *
     * This method is called when the component is initialized.
     */
    public function mount(?int $eventId = null): void
    {
        // If an event ID is provided in the query parameters, use that event
        if ($eventId && Event::find($eventId)) {
            $this->selectedEventId = $eventId;
            $this->event = Event::findOrFail($eventId);

            return;
        }

        // Try to find an active event first
        $activeEvent = Event::active()->first();

        // If no active event is found, fall back to the first event
        if (! $activeEvent) {
            $activeEvent = Event::first();
        }

        // If we have an event, set it as the selected event
        if ($activeEvent) {
            $this->selectedEventId = $activeEvent->id;
            $this->event = $activeEvent;
        }
    }

    /**
     * Handle the selection of a different event.
     *
     * This method updates the component state when a user selects a different event from the dropdown.
     */
    public function selectEvent(int $eventId): void
    {
        $this->selectedEventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    /**
     * Refresh the component when a game's status changes.
     *
     * This method can be called in two ways:
     * 1. With game, oldStatus, and newStatus parameters (for action history)
     * 2. With gameId and status parameters (for backward compatibility)
     */
    #[On('gameStatusChanged')]
    public function handleGameStatusChanged(
        ?Game $game = null,
        ?GameStatus $oldStatus = null,
        ?GameStatus $newStatus = null,
        ?int $gameId = null,
        ?string $status = null
    ): void {
        // The component will automatically re-render
    }

    /**
     * Refresh the component when a player's status changes.
     */
    #[On('playerStatusChanged')]
    public function handlePlayerStatusChanged(
        ?Player $player = null,
        ?Game $game = null,
        ?bool $isLeft = null
    ): void {
        // The component will automatically re-render
    }

    /**
     * Refresh the component when a game is reordered.
     */
    #[On('gameReordered')]
    public function handleGameReordered(
        ?Game $game = null,
        ?int $oldOrder = null,
        ?int $newOrder = null
    ): void {
        // The component will automatically re-render
    }

    /**
     * Get games that are currently being played.
     */
    public function getCurrentlyPlayingGames(): Collection
    {
        return $this->event->games()
            ->where('status', GameStatus::Playing)
            ->with(['players', 'owners', 'gamePoints.player'])
            ->get();
    }

    /**
     * Get games that are ready to start.
     */
    public function getReadyToStartGames(): Collection
    {
        return $this->event->games()
            ->where('status', GameStatus::Ready)
            ->with(['players', 'owners'])
            ->orderBy('display_order', 'asc')
            ->get();
    }

    /**
     * Get games that are finished.
     */
    public function getFinishedGames(): Collection
    {
        return $this->event->games()
            ->where('status', GameStatus::Finished)
            ->with(['players', 'owners', 'gamePoints.player'])
            ->latest('updated_at')
            ->get();
    }

    /**
     * Get background games.
     */
    public function getBackgroundGames(): Collection
    {
        return $this->event->games()
            ->where('status', GameStatus::Background)
            ->with(['players', 'owners'])
            ->get();
    }

    /**
     * Move a game up in the "Ready to Start" order.
     */
    public function moveGameUp(Game $game): void
    {
        // Only allow reordering of Ready games
        if ($game->status !== GameStatus::Ready) {
            return;
        }

        // Get all Ready games in order
        $readyGames = $this->getReadyToStartGames();

        // Find the current game's index
        $currentIndex = $readyGames->search(function ($item) use ($game) {
            return $item->id === $game->id;
        });

        // If it's already at the top, do nothing
        if ($currentIndex === 0) {
            return;
        }

        // Get the game above this one
        $aboveGame = $readyGames[$currentIndex - 1];

        // Store original display orders for undo functionality
        $oldOrder = $game->display_order;
        $newOrder = $aboveGame->display_order;

        // Swap display_order values
        $tempOrder = $game->display_order;
        $game->display_order = $aboveGame->display_order;
        $aboveGame->display_order = $tempOrder;

        // Save both games
        $game->save();
        $aboveGame->save();

        // Dispatch event for action history
        $this->dispatch('gameReordered',
            game: $game,
            oldOrder: $oldOrder,
            newOrder: $newOrder
        );

        // Show success notification
        $this->dispatch('success', "Moved {$game->name} up in queue");
    }

    /**
     * Move a game down in the "Ready to Start" order.
     */
    public function moveGameDown(Game $game): void
    {
        // Only allow reordering of Ready games
        if ($game->status !== GameStatus::Ready) {
            return;
        }

        // Get all Ready games in order
        $readyGames = $this->getReadyToStartGames();

        // Find the current game's index
        $currentIndex = $readyGames->search(function ($item) use ($game) {
            return $item->id === $game->id;
        });

        // If it's already at the bottom, do nothing
        if ($currentIndex === $readyGames->count() - 1) {
            return;
        }

        // Get the game below this one
        $belowGame = $readyGames[$currentIndex + 1];

        // Store original display orders for undo functionality
        $oldOrder = $game->display_order;
        $newOrder = $belowGame->display_order;

        // Swap display_order values
        $tempOrder = $game->display_order;
        $game->display_order = $belowGame->display_order;
        $belowGame->display_order = $tempOrder;

        // Save both games
        $game->save();
        $belowGame->save();

        // Dispatch event for action history
        $this->dispatch('gameReordered',
            game: $game,
            oldOrder: $oldOrder,
            newOrder: $newOrder
        );

        // Show success notification
        $this->dispatch('success', "Moved {$game->name} down in queue");
    }

    /**
     * Get events grouped by their status.
     *
     * This method returns a collection of events grouped by their status (ongoing, upcoming, past).
     *
     * @return array<string, \Illuminate\Database\Eloquent\Collection<int, Event>>
     */
    public function getGroupedEvents(): array
    {
        return [
            'ongoing' => Event::ongoing()->orderBy('name')->get(),
            'upcoming' => Event::upcoming()->orderBy('name')->get(),
            'past' => Event::past()->orderBy('name')->get(),
        ];
    }

    /**
     * Render the component.
     *
     * This method returns the view that will be rendered by the component.
     */
    public function render(): View
    {
        return view('livewire.event-runner', [
            'event' => $this->event,
            'currentlyPlayingGames' => $this->getCurrentlyPlayingGames(),
            'readyToStartGames' => $this->getReadyToStartGames(),
            'finishedGames' => $this->getFinishedGames(),
            'backgroundGames' => $this->getBackgroundGames(),
            'groupedEvents' => $this->getGroupedEvents(),
        ]);
    }
}
