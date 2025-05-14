<?php

use App\Models\Event;
use function Livewire\Volt\{state, rules, mount, computed, protect};

// Define state properties
state([
    'name' => '',
    'active' => false,
    'starts_at' => null,
    'ends_at' => null,
    'parentId' => null,
]);

// Mount component with initial data
mount(function ($parentId = null) {
    if (!empty($parentId)) {
        $this->parentId = $parentId;
    }
});

// Set validation rules
rules([
    'name' => ['required', 'string', 'max:255'],
    'active' => ['boolean'],
    'starts_at' => ['nullable', 'date'],
    'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
])->messages([
    'ends_at.after_or_equal' => 'The end date must be after or equal to the start date.',
]);

// Create an event action
$create = function () {
    $this->validate();

    try {
        $event = Event::create([
            'name' => $this->name,
            'active' => $this->active,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ]);

        // Reset form fields
        $this->reset(['name', 'active', 'starts_at', 'ends_at']);

        // Show a success message
        session()->flash('success', 'Event created successfully');

        // Dispatch event for parent components
        if ($this->parentId) {
            $this->dispatch('event-created')->to($this->parentId);
        }

        // Dispatch a global event to refresh the EventsTable component
        $this->dispatch('event-created');
    } catch (Exception $e) {
        session()->flash('error', 'Failed to create event: '.$e->getMessage());
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

        <flux:checkbox
            wire:model="active"
            :label="__('Active')"
        />

        <flux:input
            wire:model="starts_at"
            :label="__('Start Date')"
            type="datetime-local"
            autocomplete="off"
        />

        <flux:input
            wire:model="ends_at"
            :label="__('End Date')"
            type="datetime-local"
            autocomplete="off"
            :description="__('Must be after or equal to the start date')"
        />

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
                <flux:button
                    href="{{ route('admin.events.index') }}"
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
                    <span wire:loading.remove wire:target="create">{{ __('Create Event') }}</span>
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
