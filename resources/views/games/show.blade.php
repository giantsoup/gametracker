<x-layouts.app :title="$game->name">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <flux:button href="{{ route('games.index') }}" variant="subtle" size="sm" icon="arrow-left" wire:navigate />
                <span class="text-sm text-zinc-500 dark:text-zinc-400">Games</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $game->name }}</h1>
                <flux:button
                    href="{{ route('games.edit', $game) }}"
                    variant="primary"
                    size="sm"
                    wire:navigate
                >
                    {{ __('Edit Game') }}
                </flux:button>
            </div>
        </div>

        {{-- Game Details --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6 mb-6">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Game Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Name</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $game->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Event</p>
                    <p class="mt-1 text-sm">
                        <a href="{{ route('events.show', $game->event) }}" class="text-accent hover:underline" wire:navigate>
                            {{ $game->event->name }}
                        </a>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Duration</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $game->getDurationForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Created</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $game->created_at->format('F j, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Game Points Section --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Game Points</h2>
                <flux:button
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'assign-points-modal')"
                    variant="primary"
                    size="sm"
                >
                    {{ __('Assign Points') }}
                </flux:button>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Points assigned to players for this game.</p>
            <livewire:game-points.display-game-points :game="$game" />
        </div>
    </div>

    {{-- Assign Points Modal --}}
    <x-modal name="assign-points-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                {{ __('Assign Points for') }} {{ $game->name }}
            </h2>

            <div class="mt-6">
                <livewire:game-points.assign-points :game="$game" />
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button x-on:click="$dispatch('close')" variant="outline">
                    {{ __('Close') }}
                </flux:button>
            </div>
        </div>
    </x-modal>
</x-layouts.app>
