<div>
    <div class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Add Game</h3>
            <flux:button
                wire:click="toggleForm"
                variant="primary"
                class="transition-all duration-200"
            >
                {{ $showForm ? 'Cancel' : 'Add Game' }}
            </flux:button>
        </div>

        @if ($showForm)
            <div
                class="mt-4 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded-lg p-5 shadow-sm">
                <form wire:submit.prevent="createGame">
                    <div class="space-y-6">
                        <!-- Game Name Field -->
                        <div>
                            <flux:input
                                wire:model="name"
                                id="name"
                                label="Game Name"
                                type="text"
                                placeholder="Enter game name"
                                required
                                autocomplete="off"
                            />
                            @error('name')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <!-- Duration Field -->
                        <div>
                            <flux:label for="duration" value="Duration (minutes)"/>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach ([30, 45, 60, 90, 120] as $durationOption)
                                    <label
                                        for="duration-{{ $durationOption }}"
                                        class="relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            wire:model.live="duration"
                                            id="duration-{{ $durationOption }}"
                                            type="radio"
                                            value="{{ $durationOption }}"
                                            class="sr-only peer"
                                            name="duration"
                                        >
                                        <div class="px-4 py-2 rounded-md border border-neutral-300 dark:border-neutral-600
                                                  peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600
                                                  peer-focus:ring-2 peer-focus:ring-indigo-500 peer-focus:ring-offset-2
                                                  bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300
                                                  hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                                            {{ $durationOption }} min
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <flux:text class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                                Select a duration for this game
                            </flux:text>
                            @error('duration')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <!-- Game Owners Field -->
                        <div>
                            <flux:label for="selectedPlayerIds" value="Game Owners (Optional)"/>
                            <div class="mt-2 p-3 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                @if ($players->isEmpty())
                                    <flux:text class="text-sm text-neutral-500 dark:text-neutral-400">
                                        No players available. Add players to the event first.
                                    </flux:text>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach ($players as $player)
                                            <div class="flex items-center">
                                                <input
                                                    wire:model="selectedPlayerIds"
                                                    id="player-{{ $player->id }}"
                                                    type="checkbox"
                                                    value="{{ $player->id }}"
                                                    class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-indigo-600"
                                                >
                                                <label for="player-{{ $player->id }}"
                                                       class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">
                                                    {{ $player->getDisplayName() }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @error('selectedPlayerIds')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <!-- Points Distribution Field -->
                        <div>
                            <flux:label value="Points Distribution"/>
                            <div class="mt-2 p-4 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <livewire:games.points-distribution-config wire:key="points-distribution"/>
                            </div>
                            @error('pointsDistribution')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-2">
                            <flux:button
                                type="submit"
                                variant="primary"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75"
                            >
                                <span wire:loading.remove wire:target="createGame">Add Game</span>
                                <span wire:loading wire:target="createGame" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </flux:button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
