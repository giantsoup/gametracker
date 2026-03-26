<x-layouts.app :title="$event->name">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <flux:button href="{{ route('events.index') }}" variant="subtle" size="sm" icon="arrow-left" wire:navigate />
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Events</span>
                </div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $event->name }}</h1>
            </div>
            @if($event->active)
                <flux:badge color="green" variant="pill">Active</flux:badge>
            @else
                <flux:badge color="zinc" variant="pill">Inactive</flux:badge>
            @endif
        </div>

        {{-- Event Details --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6 mb-6">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Event Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Name</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $event->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Status</p>
                    <p class="mt-1 text-sm">
                        @if($event->active)
                            <flux:badge color="green" variant="pill" size="sm">Active</flux:badge>
                        @else
                            <flux:badge color="zinc" variant="pill" size="sm">Inactive</flux:badge>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Start Date</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ $event->starts_at ? $event->starts_at->format('F j, Y g:i A') : 'Not set' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">End Date</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ $event->ends_at ? $event->ends_at->format('F j, Y g:i A') : 'Not set' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Players Section --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6 mb-6">
            <livewire:events.players-list :event="$event"/>
            <livewire:events.create-player-form :event="$event"/>
        </div>

        {{-- Games Section --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6">
            <livewire:events.games-list :event="$event"/>
            <livewire:events.create-game-form :event="$event"/>
        </div>
    </div>
</x-layouts.app>
