<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Live Event Dashboard</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage and track your gaming events in real-time.</p>
    </div>

    <!-- Event Switcher -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Active Events</h3>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Event
            </a>
        </div>

        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($events as $event)
                    <div
                        wire:click="switchEvent({{ $event->id }})"
                        class="cursor-pointer p-4 rounded-lg border-2 {{ $activeEvent && $activeEvent->id == $event->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}"
                    >
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $event->name }}</h4>
                            @if($activeEvent && $activeEvent->id == $event->id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    Active
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            @if($event->started_at)
                                <p>Started: {{ $event->started_at->format('M d, H:i') }}</p>
                            @endif
                            @if($event->ends_at)
                                <p>Ends: {{ $event->ends_at->format('M d, H:i') }}</p>
                            @endif
                            <p>Games: {{ $event->games->count() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">No active events found. Create a new event to get started.</p>
            </div>
        @endif
    </div>

    <!-- Current Event Details -->
    @if($activeEvent)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border-t-4 border-indigo-500">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $activeEvent->name }}</h3>
                        @if($activeEvent->started_at)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Started: {{ $activeEvent->started_at->format('M d, H:i') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('events.edit', $activeEvent) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900 border border-transparent rounded-md font-medium text-xs text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Edit Event
                        </a>
                        <a href="{{ route('games.create') }}?event={{ $activeEvent->id }}" class="inline-flex items-center px-3 py-1.5 bg-green-100 dark:bg-green-900 border border-transparent rounded-md font-medium text-xs text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Game
                        </a>
                    </div>
                </div>

                <!-- Current Game Section -->
                @if($currentGame)
                    <div class="mt-6 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Current Game: {{ $currentGame->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Running for: {{ $gameDuration }}</p>
                            </div>
                            <a href="{{ route('games.edit', $currentGame) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit Game
                            </a>
                        </div>

                        @if($currentGame->owners->count() > 0)
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">Players:</h5>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($currentGame->owners as $owner)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $owner->display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-6 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg text-center">
                        <p class="text-gray-600 dark:text-gray-400">No active game for this event.</p>
                        <a href="{{ route('games.create') }}?event={{ $activeEvent->id }}" class="mt-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Start a Game
                        </a>
                    </div>
                @endif

                <!-- Upcoming Games Section -->
                @if($upcomingGames->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Upcoming Games</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($upcomingGames as $game)
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <h5 class="font-medium text-gray-900 dark:text-white">{{ $game->name }}</h5>
                                        <a href="{{ route('games.edit', $game) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <p>Duration: {{ $game->getDurationForHumans() }}</p>
                                        @if($game->owners->count() > 0)
                                            <p class="mt-1">
                                                <span class="font-medium">Owned by:</span>
                                                {{ $game->owners->pluck('display_name')->implode(', ') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Finished Games Section -->
                @if($finishedGames->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Finished Games</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Players</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($finishedGames as $game)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $game->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $game->getDurationForHumans() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $game->owners->count() }}
                                                @if($game->owners->count() > 0)
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                                        ({{ $game->owners->pluck('display_name')->implode(', ') }})
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('games.edit', $game) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-600 dark:text-gray-400">No active event selected. Please select or create an event to get started.</p>
            <a href="{{ route('events.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Create Event
            </a>
        </div>
    @endif
</div>
