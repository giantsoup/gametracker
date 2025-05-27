<?php

use App\Models\Game;
use App\Models\Event;
use function Livewire\Volt\{state, rules, mount, computed, protect};
use Illuminate\Support\Facades\Redirect;

// Define state properties
state([
    'game' => null,
    'name' => '',
    'event_id' => null,
    'duration' => 60,
    'events' => [],
    'parentId' => null,
    'referrer' => null,
]);

// Set validation rules
rules([
    'name' => ['required', 'string', 'max:255'],
    'event_id' => ['required', 'exists:events,id'],
    'duration' => ['required', 'integer', 'min:15'],
])->messages([
    'event_id.required' => 'Please select an event.',
    'event_id.exists' => 'The selected event does not exist.',
    'duration.min' => 'Duration must be at least 15 minutes.',
]);

// Mount component with initial data
mount(function ($game, $parentId = null) {
    // Set the game
    $this->game = $game;

    // Initialize form fields with game data
    $this->name = $game->name;
    $this->event_id = $game->event_id;
    $this->duration = $game->duration;

    // Load all active events for the dropdown
    $this->events = Event::orderBy('name')->get();

    if (!empty($parentId)) {
        $this->parentId = $parentId;
    }

    // Store the referrer URL if available
    $this->referrer = request()->headers->get('referer');
});

// Update a game action
$update = function () {
    $this->validate();

    try {
        $this->game->update([
            'name' => $this->name,
            'event_id' => $this->event_id,
            'duration' => $this->duration,
        ]);

        // Show a success message
        session()->flash('success', 'Game updated successfully');

        // Dispatch event for parent components
        if ($this->parentId) {
            $this->dispatch('game-updated')->to($this->parentId);
        }

        // Dispatch a global event to refresh the GamesTable component
        $this->dispatch('game-updated');

        // Determine where to redirect based on the referrer
        if ($this->referrer && str_contains($this->referrer, 'index')) {
            // Redirect to index page if they came from there
            return $this->redirect(route('games.index'), navigate: true);
        } else {
            // Default to show page
            return $this->redirect(route('games.show', $this->game), navigate: true);
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to update game: '.$e->getMessage());
        return null;
    }
};

?>

<section class="w-full">
    <form wire:submit="update" class="w-full space-y-6">

        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
        />

        <flux:select
            wire:model="event_id"
            :label="__('Event')"
            required
        >
            <option value="">{{ __('Select an event') }}</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
            @endforeach
        </flux:select>

        <flux:input
            wire:model="duration"
            :label="__('Duration (minutes)')"
            type="number"
            min="15"
            step="15"
            required
            :description="__('Duration in minutes, must be in 15-minute intervals')"
        />

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
                <flux:button
                    href="{{ route('games.index') }}"
                    wire:navigate
                    variant="outline"
                >
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="update">{{ __('Update Game') }}</span>
                    <span wire:loading wire:target="update" class="flex items-center">
                        <flux:icon icon="arrow-path" class="mr-2 h-4 w-4 animate-spin"/>
                        {{ __('Updating...') }}
                    </span>
                </flux:button>
            </div>

            @if(session('success'))
                <flux:text class="font-medium !text-green-600 !dark:text-green-400">
                    {{ session('success') }}
                </flux:text>
            @endif

            @if(session('error'))
                <flux:text class="font-medium !text-red-600 !dark:text-red-400">
                    {{ session('error') }}
                </flux:text>
            @endif
        </div>
    </form>
</section>
