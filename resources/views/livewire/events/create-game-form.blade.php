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
                            <flux:input
                                wire:model.blur="total_points"
                                wire:change="regeneratePointsDistribution"
                                id="total_points"
                                label="Total points"
                                type="number"
                                min="1"
                                required
                                help="Changing total points resets the placement values to a balanced default distribution"
                            />
                            @error('total_points')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model.blur="total_placements"
                                wire:change="regeneratePointsDistribution"
                                id="total_placements"
                                label="Total placements"
                                type="number"
                                min="1"
                                required
                                help="Changing total placements resets the placement values to a balanced default distribution"
                            />
                            @error('total_placements')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div class="space-y-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Placement values</p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                        Adjust each placement directly. The total points field updates automatically.
                                    </p>
                                </div>

                                <flux:button
                                    type="button"
                                    variant="outline"
                                    wire:click="regeneratePointsDistribution"
                                >
                                    Reset Distribution
                                </flux:button>
                            </div>

                            @error('points_distribution')
                            <flux:text class="text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror

                            <div class="space-y-3">
                                @foreach ($points_distribution as $index => $points)
                                    <div
                                        wire:key="event-placement-{{ $index }}"
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
                                                +
                                            </flux:button>

                                            <flux:button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                wire:click="decreasePlacementPoints({{ $index }})"
                                            >
                                                -
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
