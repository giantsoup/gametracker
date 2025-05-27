<div class="relative">
    <!-- Layout Switcher - Hovering Menu -->
    <div class="fixed bottom-4 right-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-2 flex space-x-2">
            <button
                wire:click="switchLayout(1)"
                class="p-2 rounded-md {{ $activeLayout == 1 ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                title="Layout 1 - Focus on current game"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                </svg>
            </button>
            <button
                wire:click="switchLayout(2)"
                class="p-2 rounded-md {{ $activeLayout == 2 ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                title="Layout 2 - Split view"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </button>
            <button
                wire:click="switchLayout(3)"
                class="p-2 rounded-md {{ $activeLayout == 3 ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                title="Layout 3 - Card Grid"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Layout 1: Focus on current game with sidebar for upcoming events -->
    @if($activeLayout == 1)
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">GameTracker Live Dashboard</h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">Real-time updates of gaming events</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content - Current Game -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    @if($activeEvent && $currentGame)
                        <div class="relative">
                            <!-- Game Header with Duration Badge -->
                            <div class="bg-indigo-600 dark:bg-indigo-800 p-6 relative">
                                <div class="absolute top-4 right-4 bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-300 px-3 py-1 rounded-full text-sm font-medium">
                                    Running for: {{ $gameDuration }}
                                </div>
                                <h2 class="text-2xl font-bold text-white">Current Game</h2>
                                <p class="text-indigo-100">{{ $activeEvent->name }}</p>
                            </div>

                            <!-- Game Details -->
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $currentGame->name }}</h3>
                                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm font-medium">
                                        Active
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Duration</h4>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $currentGame->getDurationForHumans() }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Players</h4>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $currentGame->owners->count() }} participating</p>
                                        @if($currentGame->owners->count() > 0)
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                <p class="font-medium">Owned by:</p>
                                                <ul class="mt-1 space-y-1">
                                                    @foreach($currentGame->owners as $owner)
                                                        <li>{{ $owner->display_name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($activeEvent->started_at)
                                    <div class="mt-6">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Event Progress</h4>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                            <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: 45%"></div>
                                        </div>
                                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <span>Started: {{ $activeEvent->started_at->format('M d, H:i') }}</span>
                                            @if($activeEvent->ends_at)
                                                <span>Ends: {{ $activeEvent->ends_at->format('M d, H:i') }}</span>
                                            @else
                                                <span>Ongoing</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No Active Games</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">There are no active games at the moment. Check back later or view upcoming events.</p>
                        </div>
                    @endif
                </div>

                <!-- Sidebar - Game Lists -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <!-- Upcoming Games Section - Prominently Displayed -->
                    <div class="bg-yellow-600 dark:bg-yellow-800 p-4">
                        <h2 class="text-xl font-bold text-white">Upcoming Games</h2>
                    </div>
                    <div class="p-5">
                        @if($upcomingGames->count() > 0)
                            <div class="space-y-5">
                                @foreach($upcomingGames as $game)
                                    <div class="border-l-4 border-yellow-500 dark:border-yellow-600 pl-3 py-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-r-md">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                        </div>
                                        @if($game->owners->count() > 0)
                                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Owned by:</span>
                                                {{ $game->owners->pluck('display_name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-600 dark:text-gray-400">No upcoming games scheduled.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Finished Games Section - Less Prominent -->
                    <div class="bg-blue-500 dark:bg-blue-700 p-3 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-bold text-white">Previously Played</h2>
                    </div>
                    <div class="p-3">
                        @if($finishedGames->count() > 0)
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($finishedGames as $game)
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                        <div class="flex justify-between items-center text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <span>{{ $game->getDurationForHumans() }}</span>
                                            <span>{{ $game->owners->count() }} players</span>
                                        </div>
                                        @if($game->owners->count() > 0)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                <span class="font-medium">Owned by:</span>
                                                {{ $game->owners->pluck('display_name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">No finished games yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Layout 2: Split view with equal emphasis on current and upcoming -->
    @if($activeLayout == 2)
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">GameTracker Dashboard</h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">Current and upcoming gaming events</p>
            </div>

            <!-- Current Event Section -->
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    Now Playing
                </h2>

                @if($activeEvent && $currentGame)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="md:flex">
                            <div class="md:w-2/3 p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currentGame->name }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $activeEvent->name }}</p>
                                    </div>
                                    <div class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $gameDuration }}
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mr-2">
                                            <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: 45%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">45%</span>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Started</span>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $activeEvent->started_at ? $activeEvent->started_at->format('M d, H:i') : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Duration</span>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $currentGame->getDurationForHumans() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="md:w-1/3 bg-indigo-50 dark:bg-indigo-900/30 p-6 flex flex-col justify-center">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ $currentGame->owners->count() }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Players Participating</div>
                                </div>

                                <div class="mt-6">
                                    <div class="flex -space-x-2 justify-center">
                                        @foreach(range(1, min(5, $currentGame->owners->count())) as $i)
                                            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-xs font-medium text-gray-800 dark:text-gray-200 border-2 border-white dark:border-gray-800">
                                                P{{ $i }}
                                            </div>
                                        @endforeach

                                        @if($currentGame->owners->count() > 5)
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-medium text-indigo-800 dark:text-indigo-200 border-2 border-white dark:border-gray-800">
                                                +{{ $currentGame->owners->count() - 5 }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($currentGame->owners->count() > 0)
                                    <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                                        <p class="font-medium">Owned by:</p>
                                        <p class="mt-1">{{ $currentGame->owners->pluck('display_name')->implode(', ') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No Active Games</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">There are no active games at the moment.</p>
                    </div>
                @endif
            </div>

            <!-- Upcoming Games Section - Prominently Displayed -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <span class="inline-block w-4 h-4 bg-yellow-500 rounded-full mr-2"></span>
                    Coming Up Next
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($upcomingGames as $game)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-l-4 border-yellow-500 dark:border-yellow-600 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                    <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 px-3 py-1 rounded-full text-sm font-medium">
                                        Upcoming
                                    </span>
                                </div>

                                <div class="mt-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                    </div>

                                    @if($game->owners->count() > 0)
                                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Owned by:</span>
                                            {{ $game->owners->pluck('display_name')->implode(', ') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                            <p class="text-gray-600 dark:text-gray-400">No upcoming games scheduled.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Finished Games Section - Less Prominent -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                    Previously Played
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($finishedGames as $game)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border-t border-blue-300 dark:border-blue-800">
                            <div class="p-4">
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">{{ $game->name }}</h3>

                                <div class="flex justify-between items-center text-xs text-gray-600 dark:text-gray-400">
                                    <span>{{ $game->getDurationForHumans() }}</span>
                                    <span>{{ $game->owners->count() }} players</span>
                                </div>
                                @if($game->owners->count() > 0)
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        <span class="font-medium">Owned by:</span>
                                        {{ $game->owners->pluck('display_name')->implode(', ') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">No finished games yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Layout 3: Card Grid with emphasis on upcoming games -->
    @if($activeLayout == 3)
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Game Cards</h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">View all games at a glance</p>
            </div>

            <!-- Current Game Section -->
            @if($activeEvent && $currentGame)
                <div class="mb-10">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 border-green-500 dark:border-green-600">
                        <div class="bg-green-500 dark:bg-green-600 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-white">Now Playing</h2>
                                <div class="bg-white dark:bg-gray-900 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $gameDuration }}
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currentGame->name }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $activeEvent->name }}</p>
                                </div>

                                <div class="mt-4 md:mt-0">
                                    <div class="flex items-center justify-end">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">{{ $currentGame->owners->count() }} players</span>
                                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm font-medium">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mr-2">
                                        <div class="bg-green-500 dark:bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">45%</span>
                                </div>

                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <span>Started: {{ $activeEvent->started_at ? $activeEvent->started_at->format('H:i') : 'N/A' }}</span>
                                    <span>Duration: {{ $currentGame->getDurationForHumans() }}</span>
                                </div>

                                @if($currentGame->owners->count() > 0)
                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p class="font-medium">Owned by:</p>
                                        <p class="mt-1">{{ $currentGame->owners->pluck('display_name')->implode(', ') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-10">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No Active Games</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">There are no active games at the moment.</p>
                    </div>
                </div>
            @endif

            <!-- Upcoming Games Section - Prominently Displayed -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <span class="inline-block w-4 h-4 bg-yellow-500 rounded-full mr-2"></span>
                    Coming Up Next
                </h2>

                @if($upcomingGames->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($upcomingGames as $game)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-l-4 border-yellow-500 dark:border-yellow-600 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                        <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 px-3 py-1 rounded-full text-sm font-medium">
                                            Upcoming
                                        </span>
                                    </div>

                                    <div class="mt-4">
                                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                        </div>

                                        @if($game->owners->count() > 0)
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Owned by:</span>
                                                {{ $game->owners->pluck('display_name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                        <p class="text-gray-600 dark:text-gray-400">No upcoming games scheduled.</p>
                    </div>
                @endif
            </div>

            <!-- Finished Games Section - Less Prominent -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                    Previously Played
                </h2>

                @if($finishedGames->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($finishedGames as $game)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border-t border-blue-300 dark:border-blue-800">
                                <div class="p-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $game->name }}</h3>

                                    <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ $game->getDurationForHumans() }}</span>
                                        <span>{{ $game->owners->count() }} players</span>
                                    </div>
                                    @if($game->owners->count() > 0)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <span class="font-medium">Owned by:</span>
                                            {{ $game->owners->pluck('display_name')->implode(', ') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
                        <p class="text-gray-600 dark:text-gray-400">No finished games yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
