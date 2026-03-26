<?php

use App\Models\Game;
use App\Models\Event;
use function Livewire\Volt\{state, rules, mount, computed, protect};

// Define state properties
state([
    'name' => '',
    'event_id' => null,
    'duration' => 60,
    'total_points' => Game::DEFAULT_TOTAL_POINTS,
    'total_placements' => Game::DEFAULT_TOTAL_PLACEMENTS,
    'points_distribution' => Game::defaultPointsDistribution(),
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
    'duration' => ['required', 'integer', 'min:15', 'multiple_of:15'],
    'total_points' => ['required', 'integer', 'min:1'],
    'total_placements' => ['required', 'integer', 'min:1'],
    'points_distribution' => ['required', 'array', 'list'],
    'points_distribution.*' => ['required', 'integer', 'min:0'],
])->messages([
    'event_id.required' => 'Please select an event.',
    'event_id.exists' => 'The selected event does not exist.',
    'duration.min' => 'Duration must be at least 15 minutes.',
    'duration.multiple_of' => 'Duration must be in 15-minute intervals.',
    'total_points.min' => 'Total points must be at least 1.',
    'total_placements.min' => 'Total placements must be at least 1.',
]);

// Keep the distribution list aligned with placement count and total points.
$regeneratePointsDistribution = function () {
    $this->total_points = max(1, (int) $this->total_points);
    $this->total_placements = max(1, (int) $this->total_placements);
    $this->points_distribution = Game::defaultPointsDistribution(
        $this->total_points,
        $this->total_placements,
    );
    $this->resetValidation();
};

$syncTotalPoints = function () {
    $this->total_placements = max(1, (int) $this->total_placements);
    $this->points_distribution = Game::normalizePointsDistribution(
        $this->points_distribution,
        $this->total_placements,
    );
    $this->total_points = Game::sumPointsDistribution($this->points_distribution);
    $this->resetValidation();
};

$increasePlacementPoints = function (int $index) {
    $this->points_distribution = Game::normalizePointsDistribution(
        $this->points_distribution,
        $this->total_placements,
    );
    $this->points_distribution[$index] = ($this->points_distribution[$index] ?? 0) + 1;
    $this->total_points = Game::sumPointsDistribution($this->points_distribution);
    $this->resetValidation();
};

$decreasePlacementPoints = function (int $index) {
    $this->points_distribution = Game::normalizePointsDistribution(
        $this->points_distribution,
        $this->total_placements,
    );
    $this->points_distribution[$index] = max(0, ($this->points_distribution[$index] ?? 0) - 1);
    $this->total_points = Game::sumPointsDistribution($this->points_distribution);
    $this->resetValidation();
};

$create = function () {
    $this->validate();
    $this->points_distribution = Game::normalizePointsDistribution(
        $this->points_distribution,
        $this->total_placements,
    );
    $this->total_points = Game::sumPointsDistribution($this->points_distribution);

    $pointsDistributionError = Game::pointsDistributionArrayValidationMessage(
        $this->points_distribution,
        $this->total_points,
        $this->total_placements,
    );

    if ($pointsDistributionError !== null) {
        $this->addError('points_distribution', $pointsDistributionError);
        return;
    }

    try {
        Game::create([
            'name' => $this->name,
            'event_id' => $this->event_id,
            'duration' => $this->duration,
            'total_points' => $this->total_points,
            'points_distribution' => $this->points_distribution,
        ]);

        // Reset form fields
        $this->reset(['name', 'duration', 'total_points', 'total_placements', 'points_distribution']);
        $this->duration = 60;
        $this->total_points = Game::DEFAULT_TOTAL_POINTS;
        $this->total_placements = Game::DEFAULT_TOTAL_PLACEMENTS;
        $this->points_distribution = Game::defaultPointsDistribution();

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

        <flux:input
            wire:model.blur="total_points"
            wire:change="regeneratePointsDistribution"
            :label="__('Total points')"
            type="number"
            min="1"
            required
            :description="__('Changing total points resets the placement values to a balanced default distribution')"
        />

        <flux:input
            wire:model.blur="total_placements"
            wire:change="regeneratePointsDistribution"
            :label="__('Total placements')"
            type="number"
            min="1"
            required
            :description="__('Changing total placements resets the placement values to a balanced default distribution')"
        />

        <div class="space-y-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Placement values') }}</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Adjust each placement directly. The total points field updates automatically.') }}
                    </p>
                </div>

                <flux:button
                    type="button"
                    variant="outline"
                    wire:click="regeneratePointsDistribution"
                >
                    {{ __('Reset Distribution') }}
                </flux:button>
            </div>

            @error('points_distribution')
                <flux:text class="text-sm !text-red-600 !dark:text-red-400">
                    {{ $message }}
                </flux:text>
            @enderror

            <div class="space-y-3">
                @foreach($points_distribution as $index => $points)
                    <div
                        wire:key="create-placement-{{ $index }}"
                        class="grid grid-cols-[minmax(0,1fr)_7rem_auto] items-center gap-3"
                    >
                        <div>
                            <x-placement-badge :placement="$index + 1" />
                        </div>

                        <input
                            type="number"
                            min="0"
                            wire:model.live="points_distribution.{{ $index }}"
                            wire:change="syncTotalPoints"
                            class="rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        <div class="flex items-center gap-2">
                            <flux:button
                                type="button"
                                size="sm"
                                variant="outline"
                                wire:click="increasePlacementPoints({{ $index }})"
                            >
                                {{ __('+') }}
                            </flux:button>

                            <flux:button
                                type="button"
                                size="sm"
                                variant="outline"
                                wire:click="decreasePlacementPoints({{ $index }})"
                            >
                                {{ __('-') }}
                            </flux:button>
                        </div>
                    </div>

                    @error("points_distribution.{$index}")
                        <flux:text class="text-sm !text-red-600 !dark:text-red-400">
                            {{ $message }}
                        </flux:text>
                    @enderror
                @endforeach
            </div>
        </div>

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
