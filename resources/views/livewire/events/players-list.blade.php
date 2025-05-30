<div>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Players</h3>
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
            <div class="overflow-x-auto rounded-lg border border-zinc-200 shadow-sm dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Player</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Nickname</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Joined At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Left At</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                        @foreach ($players as $player)
                            <tr class="transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                                    {{ $player->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                                    {{ $player->nickname ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
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
                                        <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                                    {{ $player->joined_at ? $player->joined_at->format('M j, Y g:i A') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
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
    </div>
</div>
