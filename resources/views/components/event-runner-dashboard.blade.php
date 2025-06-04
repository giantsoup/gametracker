<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Dashboard Header -->
    <header class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-4">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Game Event Dashboard</h1>
                <!-- Event Info -->
                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $event->start_date?->format('M d') ?? 'N/A' }} - {{ $event->end_date?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>{{ $event->players_count ?? 0 }} Players</span>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $event->games_count ?? 0 }} Games</span>
                    </div>
                    <div class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ $event->status }}
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="relative">
                    <button id="switch-event-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                        Switch Event
                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="event-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-600 z-10">
                        <!-- Ongoing Events -->
                        <div class="py-1">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ongoing Events</div>
                            @foreach($groupedEvents['ongoing'] as $ongoingEvent)
                                <a href="#" wire:click.prevent="selectEvent({{ $ongoingEvent->id }})" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $event->id === $ongoingEvent->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                    {{ $ongoingEvent->name }}
                                </a>
                            @endforeach
                        </div>
                        <!-- Upcoming Events -->
                        <div class="py-1">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Upcoming Events</div>
                            @foreach($groupedEvents['upcoming'] as $upcomingEvent)
                                <a href="#" wire:click.prevent="selectEvent({{ $upcomingEvent->id }})" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    {{ $upcomingEvent->name }}
                                </a>
                            @endforeach
                        </div>
                        <!-- Past Events -->
                        <div class="py-1">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Past Events</div>
                            @foreach($groupedEvents['past'] as $pastEvent)
                                <a href="#" wire:click.prevent="selectEvent({{ $pastEvent->id }})" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    {{ $pastEvent->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Currently Playing Section - Spans 2 columns on large screens -->
        <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border-t-4 border-blue-500">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        Currently Playing
                    </h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                        {{ $currentlyPlayingGames->count() }}
                    </span>
                </div>

                <div class="space-y-4 overflow-y-auto max-h-[500px] scrollbar-thin pr-1">
                    @foreach($currentlyPlayingGames as $game)
                        <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-lg overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-blue-100 dark:bg-blue-800/20 border-b border-blue-200 dark:border-blue-800/30 flex justify-between items-center">
                                <h3 class="font-medium text-blue-900 dark:text-blue-100">{{ $game->name }}</h3>
                                <div class="text-xs text-blue-700 dark:text-blue-300 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $game->duration }} min (started {{ $game->started_at?->diffForHumans() ?? 'recently' }})
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                                    <!-- Players -->
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Players</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($game->players as $player)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300">
                                                    <span class="w-2 h-2 mr-1 bg-purple-400 rounded-full"></span>
                                                    {{ $player->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Game Master -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Game Master</h4>
                                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            {{ $game->game_master }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Actions -->
                                <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                                    <button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Player
                                    </button>
                                    <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Finish Game
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Ready to Start Section -->
        <section class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border-t-4 border-yellow-500">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                        Ready to Start
                    </h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                        {{ $readyToStartGames->count() }}
                    </span>
                </div>

                <div class="space-y-4 overflow-y-auto max-h-[500px] scrollbar-thin pr-1">
                    @foreach($readyToStartGames as $game)
                        <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800/30 rounded-lg overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-yellow-100 dark:bg-yellow-800/20 border-b border-yellow-200 dark:border-yellow-800/30 flex justify-between items-center">
                                <h3 class="font-medium text-yellow-900 dark:text-yellow-100">{{ $game->name }}</h3>
                                <div class="text-xs text-yellow-700 dark:text-yellow-300 font-medium">
                                    {{ $game->duration }} min estimated
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                                    <!-- Players -->
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Players</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($game->players as $player)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900/20 dark:text-cyan-300">
                                                    <span class="w-2 h-2 mr-1 bg-cyan-400 rounded-full"></span>
                                                    {{ $player->name }}
                                                </span>
                                            @endforeach
                                            <button class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Game Master -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Game Master</h4>
                                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            {{ $game->game_master }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Actions with sort controls -->
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center">
                                        <button wire:click="moveGameUp({{ $game->id }})" class="inline-flex items-center justify-center w-6 h-6 rounded text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" title="Move up in queue">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>
                                        <button wire:click="moveGameDown({{ $game->id }})" class="inline-flex items-center justify-center w-6 h-6 rounded text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" title="Move down in queue">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            </svg>
                                            Start Game
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Recently Finished Section -->
        <section class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border-t-4 border-green-500">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        Recently Finished
                    </h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                        {{ $finishedGames->count() }}
                    </span>
                </div>

                <div class="space-y-4 overflow-y-auto max-h-[500px] scrollbar-thin pr-1">
                    @foreach($finishedGames as $game)
                        <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800/30 rounded-lg overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-green-100 dark:bg-green-800/20 border-b border-green-200 dark:border-green-800/30 flex justify-between items-center">
                                <h3 class="font-medium text-green-900 dark:text-green-100">{{ $game->name }}</h3>
                                <div class="text-xs text-green-700 dark:text-green-300 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $game->duration }} min (finished {{ $game->finished_at?->diffForHumans() ?? 'recently' }})
                                </div>
                            </div>
                            <div class="p-4">
                                <!-- Results section -->
                                <div class="mb-4">
                                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Results</h4>
                                    <div class="space-y-2">
                                        @foreach($game->players->sortByDesc('score')->values() as $index => $player)
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $index === 0 ? 'bg-yellow-400' : ($index === 1 ? 'bg-gray-300' : ($index === 2 ? 'bg-amber-600' : 'bg-gray-200 text-gray-600')) }} text-white font-bold text-xs mr-2">{{ $index + 1 }}</span>
                                                    <span class="text-sm font-medium">{{ $player->name }}</span>
                                                </div>
                                                <span class="text-sm font-semibold">{{ $player->score ?? 0 }} pts</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Game Master -->
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Game Master</h4>
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        {{ $game->game_master }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Background Games Section -->
        <section class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border-t-4 border-purple-500">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                        Background Games
                    </h2>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300">
                        {{ $backgroundGames->count() }}
                    </span>
                </div>

                <div class="space-y-4 overflow-y-auto max-h-[500px] scrollbar-thin pr-1">
                    @foreach($backgroundGames as $game)
                        <div class="bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800/30 rounded-lg overflow-hidden shadow-sm">
                            <div class="px-4 py-3 bg-purple-100 dark:bg-purple-800/20 border-b border-purple-200 dark:border-purple-800/30 flex justify-between items-center">
                                <h3 class="font-medium text-purple-900 dark:text-purple-100">{{ $game->name }}</h3>
                                <div class="text-xs text-purple-700 dark:text-purple-300 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $game->schedule ?? 'All convention' }}
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                                    <!-- Players -->
                                    <div class="flex-grow">
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Players</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($game->players->take(3) as $player)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300">
                                                    <span class="w-2 h-2 mr-1 bg-purple-400 rounded-full"></span>
                                                    {{ $player->name }}
                                                </span>
                                            @endforeach
                                            @if($game->players->count() > 3)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300">
                                                    <span class="w-2 h-2 mr-1 bg-purple-400 rounded-full"></span>
                                                    +{{ $game->players->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Game Master -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Game Master</h4>
                                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            {{ $game->game_master }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Status -->
                                <div class="mt-3 p-2 bg-purple-100 dark:bg-purple-900/20 rounded-md">
                                    <span class="text-xs text-purple-800 dark:text-purple-300">{{ $game->next_session_info ?? 'No upcoming sessions scheduled' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <!-- Add New Game Button (Fixed) -->
    <div class="fixed bottom-6 right-6">
        <button id="add-game-btn" class="inline-flex items-center justify-center h-14 w-14 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span class="sr-only">Add New Game</span>
        </button>
    </div>

    <!-- Toast Notification (Hidden by default) -->
    <div id="toast-notification" class="fixed right-4 bottom-20 transition-opacity duration-300 opacity-0 pointer-events-none transform translate-y-2">
        <div class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg id="toast-icon" class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p id="toast-title" class="text-sm font-medium text-gray-900 dark:text-gray-100">Success!</p>
                        <p id="toast-message" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Game status has been updated.</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button id="close-toast" class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Event dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const switchEventBtn = document.getElementById('switch-event-btn');
        const eventDropdown = document.getElementById('event-dropdown');

        if (switchEventBtn && eventDropdown) {
            switchEventBtn.addEventListener('click', () => {
                eventDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!switchEventBtn.contains(event.target) && !eventDropdown.contains(event.target)) {
                    eventDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
