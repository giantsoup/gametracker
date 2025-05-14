<?php

use App\Models\Event;
use function Livewire\Volt\{state, rules, mount, computed, protect};
use Illuminate\Support\Facades\Redirect;

// Define state properties
state([
    'event' => null,
    'name' => '',
    'active' => false,
    'starts_at' => null,
    'ends_at' => null,
    'parentId' => null,
    'referrer' => null,
]);

// Set validation rules
rules([
    'name' => ['required', 'string', 'max:255'],
    'active' => ['boolean'],
    'starts_at' => ['nullable', 'date'],
    'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
])->messages([
    'ends_at.after_or_equal' => 'The end date must be after or equal to the start date.',
]);

// Mount component with initial data
mount(function ($event, $parentId = null) {
    // Set the event
    $this->event = $event;

    // Initialize form fields with event data
    $this->name = $event->name;
    $this->active = $event->active;
    $this->starts_at = $event->starts_at;
    $this->ends_at = $event->ends_at;

    if (!empty($parentId)) {
        $this->parentId = $parentId;
    }

    // Store the referrer URL if available
    $this->referrer = request()->headers->get('referer');
});

// Update an event action
$update = function () {
    $this->validate();

    try {
        $this->event->update([
            'name' => $this->name,
            'active' => $this->active,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ]);

        // Show a success message
        session()->flash('success', 'Event updated successfully');

        // Dispatch event for parent components
        if ($this->parentId) {
            $this->dispatch('event-updated')->to($this->parentId);
        }

        // Dispatch a global event to refresh the EventsTable component
        $this->dispatch('event-updated');

        // Determine where to redirect based on the referrer
        if ($this->referrer && str_contains($this->referrer, 'index')) {
            // Redirect to index page if they came from there
            return $this->redirect(route('admin.events.index'), navigate: true);
        } else {
            // Default to show page
            return $this->redirect(route('admin.events.show', $this->event), navigate: true);
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to update event: '.$e->getMessage());
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
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="update">{{ __('Update Event') }}</span>
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
