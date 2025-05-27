<div class="w-full">
    <div class="space-y-4">
        @if($gamePoints->isEmpty())
            <div class="text-center py-4">
                <p class="text-zinc-500 dark:text-zinc-400">No points have been assigned for this game yet.</p>
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
                            @if($showDetails)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Assigned By
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Assigned At
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Last Modified
                                </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($gamePoints as $point)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $point['player']->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        @if($point['placement'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                                {{ $point['placement'] }}{{ __('st') }}
                                            </span>
                                        @else
                                            <span class="text-zinc-500 dark:text-zinc-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $point['points'] }}
                                    </div>
                                </td>
                                @if($showDetails)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $point['assigned_by']->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $point['assigned_at']->format('M j, Y g:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            @if($point['last_modified_at'])
                                                <span title="Modified by {{ $point['last_modified_by']->name ?? 'Unknown' }}">
                                                    {{ $point['last_modified_at']->format('M j, Y g:i A') }}
                                                </span>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#"
                                       x-data=""
                                       x-on:click.prevent="$dispatch('open-modal', 'modify-points-modal-{{ $point['id'] }}')"
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('Edit') }}
                                    </a>

                                    <!-- Modify Points Modal -->
                                    <x-modal name="modify-points-modal-{{ $point['id'] }}" :show="false" maxWidth="2xl">
                                        <div class="p-6">
                                            <livewire:game-points.modify-points :gamePoint="$point['id']" :key="'modify-'.$point['id']" />

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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-4">
                <flux:button
                    wire:click="toggleDetails"
                    variant="outline"
                >
                    {{ $showDetails ? __('Hide Details') : __('Show Details') }}
                </flux:button>
            </div>
        @endif
    </div>
</div>
