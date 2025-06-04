<div>
    <!-- Statistics Button -->
    <div class="mt-6 flex justify-center">
        <flux:button
            wire:click="showStatistics"
            size="lg"
            variant="outline"
            class="min-h-[44px] px-5 py-3 text-base hover-scale"
        >
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                View Event Statistics
            </div>
        </flux:button>
    </div>

    <!-- Statistics Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl dark:bg-neutral-800">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
                        {{ $event->name }} - Statistics
                    </h2>
                    <flux:button
                        wire:click="hideStatistics"
                        variant="outline"
                        size="lg"
                        class="min-h-[44px] min-w-[44px] rounded-full p-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </flux:button>
                </div>

                <!-- Tabs -->
                <div x-data="{ activeTab: 'players' }" class="mb-6">
                    <div class="mb-4 flex border-b border-neutral-200 dark:border-neutral-700">
                        <button
                            @click="activeTab = 'players'"
                            :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'players', 'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300': activeTab !== 'players' }"
                            class="min-h-[44px] min-w-[100px] border-b-2 px-4 py-2 text-base font-medium"
                        >
                            Players
                        </button>
                        <button
                            @click="activeTab = 'games'"
                            :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'games', 'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300': activeTab !== 'games' }"
                            class="min-h-[44px] min-w-[100px] border-b-2 px-4 py-2 text-base font-medium"
                        >
                            Games
                        </button>
                    </div>

                    <!-- Players Tab -->
                    <div x-show="activeTab === 'players'" class="space-y-6">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-white">Player Leaderboard</h3>

                        @if($playerStats->isEmpty())
                            <div class="rounded-lg border border-dashed border-neutral-300 bg-neutral-50 p-6 text-center dark:border-neutral-700 dark:bg-neutral-800/50">
                                <p class="text-neutral-500 dark:text-neutral-400">No player statistics available yet. Complete some games to see player rankings.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                    <thead class="bg-neutral-50 dark:bg-neutral-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Rank
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Player
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Games
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Wins
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Total Points
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Avg. Points
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Best Game
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                                        @foreach($playerStats as $index => $player)
                                            <tr class="{{ $index === 0 ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                                    {{ $index + 1 }}
                                                    @if($index === 0)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                        </svg>
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-900 dark:text-white">
                                                    {{ $player['name'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $player['gamesPlayed'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $player['wins'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                                    {{ $player['totalPoints'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $player['averagePoints'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    @if($player['bestGame'])
                                                        {{ $player['bestGame'] }} ({{ $player['bestScore'] }} pts)
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Games Tab -->
                    <div x-show="activeTab === 'games'" class="space-y-6">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-white">Game Summary</h3>

                        @if($gameStats->isEmpty())
                            <div class="rounded-lg border border-dashed border-neutral-300 bg-neutral-50 p-6 text-center dark:border-neutral-700 dark:bg-neutral-800/50">
                                <p class="text-neutral-500 dark:text-neutral-400">No game statistics available yet. Complete some games to see game summaries.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                    <thead class="bg-neutral-50 dark:bg-neutral-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Game
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Duration
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Players
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Winner
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                Top Score
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                                        @foreach($gameStats as $game)
                                            <tr>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                                    {{ $game['name'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $game['date'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $game['duration'] }} min
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $game['playerCount'] }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $game['winnerName'] ?? '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $game['highestScore'] > 0 ? $game['highestScore'] . ' pts' : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Event Summary -->
                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Games</div>
                                    <div class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-white">{{ $gameStats->count() }}</div>
                                </div>

                                <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Players</div>
                                    <div class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-white">{{ $playerStats->count() }}</div>
                                </div>

                                <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Avg. Game Duration</div>
                                    <div class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-white">
                                        {{ $gameStats->avg('duration') > 0 ? round($gameStats->avg('duration')) . ' min' : '-' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Avg. Players Per Game</div>
                                    <div class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-white">
                                        {{ $gameStats->avg('playerCount') > 0 ? round($gameStats->avg('playerCount'), 1) : '-' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button
                        wire:click="hideStatistics"
                        size="lg"
                        variant="outline"
                        class="min-h-[44px] px-5 py-3 text-base"
                    >
                        Close
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
