<div class="w-full">
    <div class="space-y-4">
        <div class="bg-white dark:bg-zinc-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-zinc-100">
                    Modify Points for {{ $player->name }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">
                    Game: {{ $game->name }}
                </p>
            </div>
            <div class="border-t border-zinc-200 dark:border-zinc-700 px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="placement" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Placement
                        </label>
                        <div class="mt-1">
                            <input
                                type="number"
                                id="placement"
                                wire:model="placement"
                                wire:change="calculatePointsFromPlacement"
                                min="1"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 rounded-md"
                            >
                            @error('placement')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            Enter the player's placement (1st, 2nd, 3rd, etc.)
                        </p>
                    </div>

                    <div>
                        <label for="points" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Points
                        </label>
                        <div class="mt-1">
                            <input
                                type="number"
                                id="points"
                                wire:model="points"
                                min="0"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 rounded-md"
                            >
                            @error('points')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            Points are automatically calculated based on placement, but can be manually adjusted.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button
                        wire:click="updatePoints"
                        variant="primary"
                    >
                        {{ __('Update Points') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
