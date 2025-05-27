<?php

use App\Models\Game;
use App\Models\Event;
use function Livewire\Volt\{state, rules, mount, computed, protect};

// Define state properties
state([
    'name' => '',
    'event_id' => null,
    'duration' => 60,
    'events' => [],
    'parentId' => null,
]);

// Mount component with initial data
mount(function ($parentId = null) {
    if (!empty($parentId)) {
        $this->parentId = $parentId;
    }

    // Load all active events for the dropdown
    $this->events = Event::where('active', true)->orderBy('name')->get();
});

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

// Create a game action
$create = function () {
    $this->validate();

    try {
        $game = Game::create([
            'name' => $this->name,
            'event_id' => $this->event_id,
            'duration' => $this->duration,
        ]);

        // Reset form fields
        $this->reset(['name', 'duration']);

        // Show a success message
        session()->flash('success', 'Game created successfully');

        // Dispatch event for parent components
        if ($this->parentId) {
            $this->dispatch('game-created')->to($this->parentId);
        }

        // Dispatch a global event to refresh the GamesTable component
        $this->dispatch('game-created');
    } catch (Exception $e) {
        session()->flash('error', 'Failed to create game: '.$e->getMessage());
    }
};

?>

<section class="w-full">
    <form wire:submit="create" class="w-full space-y-6">

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
                    {{ __('Close') }}
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="create">{{ __('Create Game') }}</span>
                    <span wire:loading wire:target="create" class="flex items-center">
                        <flux:icon icon="arrow-path" class="mr-2 h-4 w-4 animate-spin"/>
                        {{ __('Creating...') }}
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
