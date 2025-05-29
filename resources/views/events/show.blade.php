<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Event Details: {{ $event->name }}
        </h2>
    </x-slot:header>

    <div class="">
        <div class="mx-auto max-w-7xl">
            <!-- Dashboard Accessibility Styles -->
            <style>
                /* Focus styles for better keyboard navigation */
                *:focus-visible {
                    outline: 2px solid #4338ca; /* Indigo-700 */
                    outline-offset: 2px;
                }
            </style>

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 mb-4">Event Dashboard</h2>
                <p class="text-neutral-700 dark:text-neutral-300">View and manage this gaming event in real-time.</p>
            </div>

            <!-- Current Event Details -->
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-md overflow-hidden border-t-4 border-indigo-700">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-50">{{ $event->name }}</h3>
                            @if($event->started_at)
                                <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">
                                    Started: {{ $event->started_at->format('M d, H:i') }}
                                </p>
                            @endif
                            @if($event->ends_at)
                                <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">
                                    Ends: {{ $event->ends_at->format('M d, H:i') }}
                                </p>
                            @endif
                            <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">
                                Status:
                                @if($event->active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-700 text-white">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-neutral-500 text-white">
                                        Inactive
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center px-3 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit Event
                            </a>
                            <a href="{{ route('games.create') }}?event={{ $event->id }}" class="inline-flex items-center px-3 py-2 bg-green-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Game
                            </a>
                        </div>
                    </div>

                    <!-- Current Game Section -->
                    @if($currentGame)
                        <div class="mt-6 bg-indigo-100 dark:bg-indigo-900/30 p-5 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">Current Game: {{ $currentGame->name }}</h4>
                                    <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">Running for: {{ $gameDuration }}</p>
                                </div>
                                <a href="{{ route('games.edit', $currentGame) }}" class="inline-flex items-center px-3 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Edit Game
                                </a>
                            </div>

                            <div class="mt-5">
                                <h5 class="text-base font-semibold text-neutral-900 dark:text-neutral-50">Players:</h5>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if($currentGame->owners->count() > 0)
                                        @foreach($currentGame->owners as $owner)
                                            <div class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-blue-700 text-white">
                                                {{ $owner->display_name }}
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300">No players assigned to this game yet.</p>
                                    @endif
                                </div>

                                <!-- Points Management Section -->
                                <div class="mt-6">
                                    <h5 class="text-base font-semibold text-neutral-900 dark:text-neutral-50 mb-3">Points Management:</h5>
                                    @if($currentGame->owners->count() > 0)
                                        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 overflow-hidden">
                                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                                <thead class="bg-neutral-100 dark:bg-neutral-700">
                                                    <tr>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Player</th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Points</th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Placement</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                                                    @foreach($currentGame->owners as $owner)
                                                        @php
                                                            $gamePoint = $currentGame->points()->where('player_id', $owner->id)->first();
                                                        @endphp
                                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $owner->display_name }}</td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">{{ $gamePoint ? $gamePoint->points : '-' }}</td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">{{ $gamePoint ? $gamePoint->placement : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 p-3 bg-neutral-100 dark:bg-neutral-700 rounded-lg">Add players to the game to manage points.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-6 bg-neutral-100 dark:bg-neutral-800 p-5 rounded-lg text-center border border-neutral-300 dark:border-neutral-600">
                            <p class="text-neutral-700 dark:text-neutral-300 mb-3">No active game for this event.</p>
                            <a href="{{ route('games.create') }}?event={{ $event->id }}" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Start a Game
                            </a>
                        </div>
                    @endif

                    <!-- Upcoming Games Section -->
                    @if($upcomingGames->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50 mb-4">Upcoming Games</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                @foreach($upcomingGames as $game)
                                    <div class="bg-white dark:bg-neutral-800 border-l-4 border-amber-500 dark:border-amber-600 border-t border-r border-b border-neutral-300 dark:border-neutral-600 rounded-lg p-5 shadow-sm">
                                        <div class="flex justify-between items-start">
                                            <h5 class="font-semibold text-neutral-900 dark:text-neutral-50 text-base">{{ $game->name }}</h5>
                                            <a href="{{ route('games.edit', $game) }}" class="p-1.5 bg-amber-700 text-white rounded-md hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="mt-3 text-sm text-neutral-700 dark:text-neutral-300">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-amber-700 dark:text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                            </div>
                                            @if($game->owners->count() > 0)
                                                <div class="flex items-start mt-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 text-amber-700 dark:text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                    </svg>
                                                    <div>
                                                        <span class="font-medium">Players:</span>
                                                        {{ $game->owners->pluck('display_name')->implode(', ') }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Finished Games Section -->
                    @if($finishedGames->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50 mb-4">Finished Games</h4>
                            <div class="overflow-x-auto bg-white dark:bg-neutral-800 rounded-lg shadow-md border border-neutral-300 dark:border-neutral-600">
                                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                    <thead class="bg-neutral-100 dark:bg-neutral-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Duration</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Players</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                                        @foreach($finishedGames as $game)
                                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $game->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">{{ $game->getDurationForHumans() }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                                                    {{ $game->owners->count() }}
                                                    @if($game->owners->count() > 0)
                                                        <span class="text-xs text-neutral-600 dark:text-neutral-400 ml-1">
                                                            ({{ $game->owners->pluck('display_name')->implode(', ') }})
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('games.edit', $game) }}" class="px-3 py-1 bg-blue-700 text-white rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">Edit</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-4 pt-8">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                            {{ __('Back to Events') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
