<div class="w-full">
    <div class="space-y-4">
        @if(count($players) === 0)
            <div class="text-center py-4">
                <p class="text-zinc-500 dark:text-zinc-400">No players found for this game.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Player
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Placement
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Points
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($players as $player)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $player['name'] }}
                                            @if($player['nickname'])
                                                <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ $player['nickname'] }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <input
                                            type="number"
                                            wire:model="placements.{{ $player['id'] }}"
                                            wire:change="calculatePointsFromPlacement({{ $player['id'] }})"
                                            min="1"
                                            class="w-20 rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                        @error("placements.{$player['id']}")
                                            <span class="text-red-600 text-xs ml-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <input
                                            type="number"
                                            wire:model="playerPoints.{{ $player['id'] }}"
                                            min="0"
                                            class="w-20 rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                        @error("playerPoints.{$player['id']}")
                                            <span class="text-red-600 text-xs ml-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-4">
                <flux:button
                    wire:click="savePoints"
                    variant="primary"
                >
                    {{ __('Save Points') }}
                </flux:button>
            </div>
        @endif
    </div>
</div>
