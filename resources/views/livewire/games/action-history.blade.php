<div class="fixed bottom-20 right-4 z-40 md:bottom-4">
    <!-- History Button -->
    <div class="flex justify-end">
        <flux:button
            wire:click="toggleExpanded"
            size="lg"
            variant="{{ $isExpanded ? 'primary' : 'outline' }}"
            class="rounded-full min-h-[44px] min-w-[44px] p-2 shadow-lg hover-scale"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </flux:button>
    </div>

    <!-- History Panel -->
    <div
        x-data="{ show: @entangle('isExpanded') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        class="mt-2 w-80 rounded-lg border border-neutral-200 bg-white p-4 shadow-lg dark:border-neutral-700 dark:bg-neutral-800"
    >
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-base font-medium text-neutral-900 dark:text-white">Recent Actions</h3>

            <div class="flex space-x-2">
                <flux:button
                    wire:click="clearHistory"
                    size="lg"
                    variant="outline"
                    class="min-h-[44px] min-w-[44px] rounded-full p-2"
                    title="Clear History"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </flux:button>

                <flux:button
                    wire:click="toggleExpanded"
                    size="lg"
                    variant="outline"
                    class="min-h-[44px] min-w-[44px] rounded-full p-2"
                    title="Close"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </flux:button>
            </div>
        </div>

        @if($actions->isEmpty())
            <div class="rounded-lg border border-dashed border-neutral-300 bg-neutral-50 p-4 text-center dark:border-neutral-700 dark:bg-neutral-800/50">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">No actions recorded yet.</p>
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Actions will appear here as you make changes.</p>
            </div>
        @else
            <div class="max-h-96 space-y-2 overflow-y-auto">
                @foreach($actions as $index => $action)
                    <div class="rounded-lg border border-neutral-200 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800 {{ isset($action['undone']) && $action['undone'] ? 'opacity-60' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm text-neutral-700 dark:text-neutral-300">
                                    {{ $action['message'] }}
                                </p>
                                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ \Carbon\Carbon::parse($action['timestamp'])->diffForHumans() }}
                                </p>
                            </div>

                            @if($action['can_undo'] ?? false)
                                <flux:button
                                    wire:click="undoAction({{ $index }})"
                                    size="lg"
                                    variant="outline"
                                    class="ml-2 min-h-[44px] min-w-[44px] rounded-full p-2"
                                    title="Undo"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                </flux:button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
