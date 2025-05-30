<div class="space-y-5">
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Total Points Input -->
            <div>
                <flux:label for="totalPoints" value="Total Points to Distribute" />
                <input
                    type="number"
                    id="totalPoints"
                    wire:model.live="totalPoints"
                    min="1"
                    class="mt-1 block w-full rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer shadow-sm py-2 h-10 leading-[1.375rem] px-3 transition-colors"
                >
                <flux:text class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                    Total number of points available for this game
                </flux:text>
            </div>

            <!-- Points Recipients Input -->
            <div>
                <flux:label for="pointsRecipients" value="Number of Players Receiving Points" />
                <input
                    type="number"
                    id="pointsRecipients"
                    wire:model.live="pointsRecipients"
                    min="1"
                    class="mt-1 block w-full rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer shadow-sm py-2 h-10 leading-[1.375rem] px-3 transition-colors"
                >
                <flux:text class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                    How many top players will receive points
                </flux:text>
            </div>
        </div>

        <!-- Distribution Toggle Button -->
        <div class="mt-4">
            <div class="flex items-center flex-wrap gap-3">
                <flux:button
                    wire:click="toggleCustomDistribution"
                    variant="primary"
                    class="transition-all duration-200"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75"
                >
                    <span wire:loading.remove wire:target="toggleCustomDistribution">
                        {{ $useCustomDistribution ? 'Use Auto Distribution' : 'Customize Distribution' }}
                    </span>
                    <span wire:loading wire:target="toggleCustomDistribution" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </flux:button>

                @if(!$isValid)
                    <flux:text class="text-sm !text-red-600 !dark:text-red-400">
                        Total of distributed points ({{ $pointsSum }}) does not match the configured total ({{ $totalPoints }})
                    </flux:text>
                @endif
            </div>
        </div>
    </div>

    <!-- Points Distribution Table -->
    <div>
        <flux:label value="Points Distribution" />

        <flux:text class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
            Configure how points will be distributed to players based on their placement
        </flux:text>

        <div class="mt-3 overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Placement
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Points
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">
                    @foreach(range(1, $pointsRecipients) as $placement)
                        <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                {{ $placement }}{{ $placement === 1 ? 'st' : ($placement === 2 ? 'nd' : ($placement === 3 ? 'rd' : 'th')) }} Place
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($useCustomDistribution)
                                    <input
                                        type="number"
                                        wire:model.live="pointsDistribution.{{ $placement }}"
                                        min="0"
                                        class="w-20 rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer shadow-sm py-1.5 h-8 leading-[1.125rem] px-2 text-sm transition-colors"
                                    >
                                @else
                                    <div class="text-neutral-700 dark:text-neutral-300 font-medium">
                                        {{ $pointsDistribution[$placement] ?? 0 }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
