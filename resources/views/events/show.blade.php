<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Event Details: {{ $event->name }}
        </h2>
    </x-slot:header>

    <div class="">
        <div class="mx-auto max-w-7xl">
            <div
                class="overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Event Information</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Details about the event.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Name</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $event->name }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</h4>
                                <p class="mt-1 text-sm">
                                    @if($event->active)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                            Inactive
                                        </span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Start Date</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $event->starts_at ? $event->starts_at->format('F j, Y g:i A') : 'Not set' }}
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">End Date</h4>
                                <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $event->ends_at ? $event->ends_at->format('F j, Y g:i A') : 'Not set' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <flux:button
                                href="{{ route('events.index') }}"
                                wire:navigate
                                variant="outline"
                            >
                                {{ __('Back to Events') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="mt-8 overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <livewire:events.players-list :event="$event"/>
                    <livewire:events.create-player-form :event="$event"/>
                </div>
            </div>

            <div
                class="mt-8 overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <livewire:events.games-list :event="$event"/>
                    <livewire:events.create-game-form :event="$event"/>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
