<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Game Details: {{ $game->name }}
        </h2>
    </x-slot:header>

    <div class="">
        <div class="mx-auto max-w-7xl">
            <div
                class="overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Game Information</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Details about the game.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Name</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $game->name }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Event</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                    <a href="{{ route('events.show', $game->event) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $game->event->name }}
                                    </a>
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Duration</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $game->getDurationForHumans() }}
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created At</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $game->created_at->format('F j, Y g:i A') }}
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Last Updated</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $game->updated_at->format('F j, Y g:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <flux:button
                                href="{{ route('games.index') }}"
                                wire:navigate
                                variant="outline"
                            >
                                {{ __('Back to Games') }}
                            </flux:button>

                            <flux:button
                                href="{{ route('games.edit', $game) }}"
                                wire:navigate
                                variant="primary"
                            >
                                {{ __('Edit Game') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Points Section -->
            <div class="mt-6 overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Game Points</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Points assigned to players for this game.</p>
                        </div>

                        <livewire:game-points.display-game-points :game="$game" />

                        <div class="mt-6">
                            <flux:button
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'assign-points-modal')"
                                variant="primary"
                            >
                                {{ __('Assign Points') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Points Modal -->
    <x-modal name="assign-points-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                {{ __('Assign Points for') }} {{ $game->name }}
            </h2>

            <div class="mt-6">
                <livewire:game-points.assign-points :game="$game" />
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button
                    x-on:click="$dispatch('close')"
                    variant="outline"
                >
                    {{ __('Close') }}
                </flux:button>
            </div>
        </div>
    </x-modal>
</x-layouts.app>
