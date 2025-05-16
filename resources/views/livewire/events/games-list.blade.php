<div>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Games</h3>
        </div>

        @if ($games->isEmpty())
            <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-600">No games found</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-500">
                            <p>There are no games in this event yet. Add a game using the form below.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-zinc-200 shadow-sm dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Game</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Owners</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                        @foreach ($games as $game)
                            <tr class="transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                                    {{ $game->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-700 dark:text-zinc-300">
                                    {{ $game->getDurationForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-zinc-700 dark:text-zinc-300">
                                    @if ($game->owners->isEmpty())
                                        <span class="text-zinc-500 dark:text-zinc-400">No owners</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($game->owners as $owner)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                                    {{ $owner->getDisplayName() }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex justify-end gap-2">
                                        <flux:button
                                            wire:click="removeGame({{ $game->id }})"
                                            wire:confirm="Are you sure you want to remove this game?"
                                            variant="ghost"
                                            size="xs"
                                            class="text-red-500/80 hover:text-red-600 hover:bg-red-50 dark:text-red-400/80 dark:hover:text-red-300 dark:hover:bg-red-950/30"
                                        >
                                            Remove
                                        </flux:button>
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
