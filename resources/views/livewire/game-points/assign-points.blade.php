<div class="w-full">
    <div class="space-y-4">
        @if($placements->isEmpty())
            <div class="py-4 text-center">
                <p class="text-zinc-500 dark:text-zinc-400">No scoring placements have been configured for this game.</p>
            </div>
        @elseif($game->owners->isEmpty())
            <div class="py-4 text-center">
                <p class="text-zinc-500 dark:text-zinc-400">No players found for this game.</p>
            </div>
        @else
            <div class="rounded-lg bg-zinc-50 px-4 py-3 text-sm text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                {{ __('Configured scoring: :distribution (:total total points)', ['distribution' => $game->formattedPointsDistribution(), 'total' => $game->total_points]) }}
            </div>

            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/60">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Placement
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Player
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Points
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                        @foreach($placements as $placement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-placement-badge :placement="$placement['number']" />
                                </td>
                                <td class="px-6 py-4">
                                    <flux:select
                                        wire:model.live="selectedPlayers.{{ $placement['number'] }}"
                                        wire:key="assign-placement-{{ $placement['number'] }}-{{ md5(json_encode($selectedPlayers)) }}"
                                        placeholder="Select a player"
                                    >
                                        <flux:select.option value="">
                                            {{ __('Select a player') }}
                                        </flux:select.option>

                                        @foreach($placement['players'] as $player)
                                            <flux:select.option value="{{ $player['id'] }}">
                                                {{ $player['display_name'] }}
                                                @if($player['nickname'] && $player['nickname'] !== $player['name'])
                                                    ({{ $player['name'] }})
                                                @endif
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>

                                    @error("selectedPlayers.{$placement['number']}")
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $placement['points'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="outline">
                        {{ __('Close') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    wire:click="resetSelections"
                    wire:loading.attr="disabled"
                    variant="outline"
                >
                    {{ __('Reset') }}
                </flux:button>

                <flux:button
                    wire:click="savePoints"
                    wire:loading.attr="disabled"
                    variant="primary"
                >
                    <span wire:loading.remove wire:target="savePoints">{{ __('Save Points') }}</span>
                    <span wire:loading wire:target="savePoints">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        @endif
    </div>
</div>
