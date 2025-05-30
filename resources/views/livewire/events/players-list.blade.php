<div>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">Players</h3>
            <button
                wire:click="toggleExpanded"
                class="p-1.5 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors"
                aria-label="{{ $isExpanded ? 'Collapse' : 'Expand' }} players list"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-500 dark:text-neutral-400 transform transition-transform duration-200 {{ $isExpanded ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        @if ($players->isEmpty())
            <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-600">No players found</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-500">
                            <p>There are no players in this event yet. Add a player using the form below.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Collapsed View (Pills) -->
            @if(!$isExpanded)
                <div class="mt-3 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                    <div class="flex items-center justify-between mb-2">
                        @php
                            $activePlayerCount = $players->filter(fn($player) => !$player->hasLeft())->count();
                        @endphp
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $activePlayerCount }} player{{ $activePlayerCount != 1 ? 's' : '' }} in this event
                        </p>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">Joined</span>
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-300">Left</span>
                        </div>
                    </div>
                    <!-- Active Players -->
                    <div class="flex flex-wrap gap-2">
                        @foreach ($players->filter(fn($player) => !$player->hasLeft()) as $player)
                            @php
                                $bgColor = 'bg-neutral-100 dark:bg-neutral-700';
                                $textColor = 'text-neutral-800 dark:text-neutral-300';

                                if ($player->trashed()) {
                                    $bgColor = 'bg-purple-100 dark:bg-purple-900/20';
                                    $textColor = 'text-purple-800 dark:text-purple-300';
                                } elseif ($player->hasJoined()) {
                                    $bgColor = 'bg-green-100 dark:bg-green-900/20';
                                    $textColor = 'text-green-800 dark:text-green-300';
                                }
                            @endphp
                            <div class="inline-flex items-center rounded-full {{ $bgColor }} px-3 py-1 text-sm font-medium {{ $textColor }}">
                                {{ $player->user->name }}
                                @if ($player->nickname)
                                    <span class="ml-1 opacity-75">({{ $player->nickname }})</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Left Players -->
                    @if($players->filter(fn($player) => $player->hasLeft())->count() > 0)
                        <div class="mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-700">
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">Left Players:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($players->filter(fn($player) => $player->hasLeft()) as $player)
                                    <div class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/20 px-3 py-1 text-sm font-medium text-red-800 dark:text-red-300">
                                        {{ $player->user->name }}
                                        @if ($player->nickname)
                                            <span class="ml-1 opacity-75">({{ $player->nickname }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Expanded View (Table) -->
                <div class="overflow-x-auto rounded-lg border border-neutral-200 shadow-sm dark:border-neutral-700">
                    <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-700">
                        <thead class="bg-neutral-50 dark:bg-neutral-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Player</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Nickname</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Joined At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Left At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">
                            @foreach ($players as $player)
                                <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                        {{ $player->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                        {{ $player->nickname ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                        @if ($player->trashed())
                                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/20 dark:text-purple-300">
                                                Deleted
                                            </span>
                                        @elseif ($player->hasLeft())
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-300">
                                                Left
                                            </span>
                                        @elseif ($player->hasJoined())
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                Joined
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                        {{ $player->joined_at ? $player->joined_at->format('M j, Y g:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                        {{ $player->left_at ? $player->left_at->format('M j, Y g:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-2">
                                            @if ($player->trashed())
                                                <flux:button
                                                    wire:click="restorePlayer({{ $player->id }})"
                                                    variant="ghost"
                                                    size="xs"
                                                    class="text-green-500/80 hover:text-green-600 hover:bg-green-50 dark:text-green-400/80 dark:hover:text-green-300 dark:hover:bg-green-950/30"
                                                >
                                                    Restore
                                                </flux:button>
                                            @else
                                                @if (!$player->hasJoined())
                                                    <flux:button
                                                        wire:click="markPlayerJoined({{ $player->id }})"
                                                        variant="ghost"
                                                        size="xs"
                                                    >
                                                        Mark Joined
                                                    </flux:button>
                                                @endif

                                                @if ($player->hasJoined() && !$player->hasLeft())
                                                    <flux:button
                                                        wire:click="markPlayerLeft({{ $player->id }})"
                                                        variant="ghost"
                                                        size="xs"
                                                    >
                                                        Mark Left
                                                    </flux:button>
                                                @endif

                                                @if ($player->hasLeft())
                                                    <flux:button
                                                        wire:click="deletePlayer({{ $player->id }})"
                                                        wire:confirm="Are you sure you want to fully remove this player? This will soft delete the record."
                                                        variant="ghost"
                                                        size="xs"
                                                        class="text-red-500/80 hover:text-red-600 hover:bg-red-50 dark:text-red-400/80 dark:hover:text-red-300 dark:hover:bg-red-950/30"
                                                    >
                                                        Delete
                                                    </flux:button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>
</div>
