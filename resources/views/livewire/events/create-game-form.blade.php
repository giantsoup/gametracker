<div>
    <div class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Add Game</h3>
            <flux:button
                wire:click="toggleForm"
                variant="primary"
            >
                {{ $showForm ? 'Cancel' : 'Add Game' }}
            </flux:button>
        </div>

        @if ($showForm)
            <div class="mt-4 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                <div class="">
                    <div class="space-y-4">
                        <div>
                            <flux:input
                                wire:model="name"
                                id="name"
                                label="Game Name"
                                type="text"
                                required
                            />
                            @error('name')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="duration"
                                id="duration"
                                label="Duration (minutes)"
                                type="number"
                                min="15"
                                step="15"
                                required
                                help="Duration must be in 15-minute intervals (e.g., 15, 30, 45, 60)"
                            />
                            @error('duration')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:label for="selectedPlayerIds" value="Game Owners (Optional)"/>
                            <div class="mt-1">
                                @if ($players->isEmpty())
                                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                        No players available. Add players to the event first.
                                    </flux:text>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($players as $player)
                                            <div class="flex items-center">
                                                <input
                                                    wire:model="selectedPlayerIds"
                                                    id="player-{{ $player->id }}"
                                                    type="checkbox"
                                                    value="{{ $player->id }}"
                                                    class="h-4 w-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700 dark:focus:ring-indigo-600"
                                                >
                                                <label for="player-{{ $player->id }}"
                                                       class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">
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

                        <div>
                            <flux:label value="Points Distribution"/>
                            <div class="mt-1">
                                <livewire:games.points-distribution-config wire:key="points-distribution" />
                            </div>
                            @error('pointsDistribution')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <flux:button
                                wire:click="createGame"
                                variant="primary"
                            >
                                Add Game
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
