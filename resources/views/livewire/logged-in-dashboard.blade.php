<div>
    <!-- Dashboard Accessibility Styles -->
    <style>
        /* Focus styles for better keyboard navigation */
        *:focus-visible {
            outline: 2px solid #4338ca; /* Indigo-700 */
            outline-offset: 2px;
        }
    </style>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 mb-4">Live Event Dashboard</h2>
        <p class="text-neutral-700 dark:text-neutral-300">Manage and track your gaming events in real-time.</p>
    </div>

    <!-- Event Switcher -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">Active Events</h3>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-800 focus:bg-indigo-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition ease-in-out duration-150">
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
                        class="cursor-pointer p-4 rounded-lg border-2 {{ $activeEvent && $activeEvent->id == $event->id ? 'border-indigo-700 bg-indigo-50 dark:bg-indigo-900/30' : 'border-neutral-300 dark:border-neutral-600 hover:border-indigo-500 dark:hover:border-indigo-500' }}"
                    >
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-neutral-900 dark:text-neutral-50">{{ $event->name }}</h4>
                            @if($activeEvent && $activeEvent->id == $event->id)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-700 text-white">
                                    Active
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">
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
            <div class="bg-neutral-100 dark:bg-neutral-800 rounded-lg p-6 text-center border border-neutral-300 dark:border-neutral-600">
                <p class="text-neutral-700 dark:text-neutral-300">No active events found. Create a new event to get started.</p>
            </div>
        @endif
    </div>

    <!-- Current Event Details -->
    @if($activeEvent)
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-md overflow-hidden border-t-4 border-indigo-700">
            <div class="p-6">
                @if($isEditingEvent && $editingEvent && $editingEvent->id === $activeEvent->id)
                    <!-- Event Edit Form -->
                    <div class="bg-neutral-100 dark:bg-neutral-700 p-5 rounded-lg mb-4 border border-neutral-300 dark:border-neutral-600">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50 mb-4">Edit Event</h3>
                        <form wire:submit.prevent="saveEvent">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="eventName" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Event Name</label>
                                    <input type="text" id="eventName" wire:model="eventName" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('eventName') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="eventStartedAt" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Start Date/Time</label>
                                    <input type="datetime-local" id="eventStartedAt" wire:model="eventStartedAt" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('eventStartedAt') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="eventEndsAt" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">End Date/Time</label>
                                    <input type="datetime-local" id="eventEndsAt" wire:model="eventEndsAt" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-indigo-600 focus:ring-indigo-600">
                                    @error('eventEndsAt') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="eventActive" wire:model="eventActive" class="rounded border-neutral-400 text-indigo-700 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 w-5 h-5">
                                    <label for="eventActive" class="ml-2 block text-sm font-medium text-neutral-900 dark:text-neutral-100">Active</label>
                                </div>

                                <div class="flex justify-end space-x-3 mt-2">
                                    <button type="button" wire:click="cancelEditEvent" class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-50">{{ $activeEvent->name }}</h3>
                            @if($activeEvent->started_at)
                                <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">
                                    Started: {{ $activeEvent->started_at->format('M d, H:i') }}
                                </p>
                            @endif
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="startEditingEvent({{ $activeEvent->id }})" class="inline-flex items-center px-3 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit Event
                            </button>
                            <button wire:click="startCreatingGame" class="inline-flex items-center px-3 py-2 bg-green-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Game
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Game Creation Form -->
                @if($isCreatingGame)
                    <div class="mt-6 bg-green-50 dark:bg-green-900/30 p-5 rounded-lg border border-green-200 dark:border-green-800">
                        <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50 mb-4">Create New Game</h4>
                        <form wire:submit.prevent="createGame">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="newGameName" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Game Name</label>
                                    <input type="text" id="newGameName" wire:model="newGameName" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-green-600 focus:ring-green-600">
                                    @error('newGameName') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="newGameDurationHours" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Duration (Hours)</label>
                                        <input type="number" id="newGameDurationHours" wire:model="newGameDurationHours" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-green-600 focus:ring-green-600">
                                        @error('newGameDurationHours') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="newGameDurationMinutes" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Duration (Minutes)</label>
                                        <input type="number" id="newGameDurationMinutes" wire:model="newGameDurationMinutes" min="0" max="59" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-green-600 focus:ring-green-600">
                                        @error('newGameDurationMinutes') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 mt-2">
                                    <button type="button" wire:click="cancelCreateGame" class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                        Create Game
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Current Game Section -->
                @if($currentGame)
                    <div class="mt-6 bg-indigo-100 dark:bg-indigo-900/30 p-5 rounded-lg border border-indigo-200 dark:border-indigo-800">
                        @if($isEditingGame && $editingGame && $editingGame->id === $currentGame->id)
                            <!-- Game Edit Form -->
                            <div class="bg-white dark:bg-neutral-800 p-5 rounded-lg mb-4 border border-neutral-300 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50 mb-4">Edit Game</h4>
                                <form wire:submit.prevent="saveGame">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label for="gameName" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Game Name</label>
                                            <input type="text" id="gameName" wire:model="gameName" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-indigo-600 focus:ring-indigo-600">
                                            @error('gameName') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="gameDurationHours" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Duration (Hours)</label>
                                                <input type="number" id="gameDurationHours" wire:model="gameDurationHours" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-indigo-600 focus:ring-indigo-600">
                                                @error('gameDurationHours') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="gameDurationMinutes" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Duration (Minutes)</label>
                                                <input type="number" id="gameDurationMinutes" wire:model="gameDurationMinutes" min="0" max="59" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-indigo-600 focus:ring-indigo-600">
                                                @error('gameDurationMinutes') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="flex justify-end space-x-3 mt-2">
                                            <button type="button" wire:click="cancelEditGame" class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">Current Game: {{ $currentGame->name }}</h4>
                                    <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">Running for: {{ $gameDuration }}</p>
                                </div>
                                <button wire:click="startEditingGame({{ $currentGame->id }})" class="inline-flex items-center px-3 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Edit Game
                                </button>
                            </div>
                        @endif

                        <div class="mt-5">
                            <div class="flex justify-between items-center">
                                <h5 class="text-base font-semibold text-neutral-900 dark:text-neutral-50">Players:</h5>
                                <button
                                    wire:click="loadAvailablePlayers({{ $activeEvent->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Player
                                </button>
                            </div>

                            @if($availablePlayers && count($availablePlayers) > 0)
                                <div class="mt-3 bg-white dark:bg-neutral-800 p-4 rounded-lg border border-neutral-300 dark:border-neutral-600">
                                    <form wire:submit.prevent="addPlayerToGame({{ $currentGame->id }})">
                                        <div class="flex items-center space-x-3">
                                            <select wire:model="selectedPlayerId" class="block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm">
                                                <option value="">Select a player</option>
                                                @foreach($availablePlayers as $player)
                                                    <option value="{{ $player->id }}">{{ $player->display_name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">
                                                Add
                                            </button>
                                        </div>
                                        @error('selectedPlayerId') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                    </form>
                                </div>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($currentGame->owners->count() > 0)
                                    @foreach($currentGame->owners as $owner)
                                        @if($isEditingPlayer && $editingPlayer && $editingPlayer->id === $owner->id)
                                            <div class="bg-white dark:bg-neutral-800 p-4 rounded-lg border border-neutral-300 dark:border-neutral-600 w-full">
                                                <form wire:submit.prevent="savePlayer">
                                                    <div class="grid grid-cols-1 gap-4">
                                                        <div>
                                                            <label for="playerNickname" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Nickname</label>
                                                            <input type="text" id="playerNickname" wire:model="playerNickname" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                        </div>

                                                        <div class="grid grid-cols-2 gap-3">
                                                            <div>
                                                                <label for="playerJoinedAt" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Joined At</label>
                                                                <input type="datetime-local" id="playerJoinedAt" wire:model="playerJoinedAt" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                            </div>
                                                            <div>
                                                                <label for="playerLeftAt" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Left At</label>
                                                                <input type="datetime-local" id="playerLeftAt" wire:model="playerLeftAt" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-end space-x-3 mt-2">
                                                            <button type="button" wire:click="cancelEditPlayer" class="inline-flex items-center px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-blue-700 text-white group">
                                                {{ $owner->display_name }}
                                                <div class="ml-2 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button wire:click="startEditingPlayer({{ $owner->id }})" class="text-white hover:text-blue-200 focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                    </button>
                                                    <button wire:click="removePlayerFromGame({{ $currentGame->id }}, {{ $owner->id }})" class="text-white hover:text-red-200 focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-sm text-neutral-700 dark:text-neutral-300">No players assigned to this game yet.</p>
                                @endif
                            </div>

                            <!-- Points Management Section -->
                            <div class="mt-6">
                                <h5 class="text-base font-semibold text-neutral-900 dark:text-neutral-50 mb-3">Points Management:</h5>

                                @if($isEditingPoints && $editingPoints)
                                    <div class="bg-white dark:bg-neutral-800 p-4 rounded-lg border border-neutral-300 dark:border-neutral-600">
                                        <h6 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50 mb-3">
                                            {{ $editingPoints->exists ? 'Edit Points' : 'Add Points for Player' }}
                                        </h6>
                                        <form wire:submit.prevent="savePoints">
                                            <div class="grid grid-cols-1 gap-4">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="pointsValue" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Points</label>
                                                        <input type="number" id="pointsValue" wire:model="pointsValue" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-purple-600 focus:ring-purple-600">
                                                        @error('pointsValue') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                                    </div>
                                                    <div>
                                                        <label for="pointsPlacement" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Placement</label>
                                                        <input type="number" id="pointsPlacement" wire:model="pointsPlacement" min="1" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-purple-600 focus:ring-purple-600">
                                                        @error('pointsPlacement') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="flex justify-end space-x-3 mt-2">
                                                    <button type="button" wire:click="cancelEditPoints" class="inline-flex items-center px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-purple-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors">
                                                        {{ $editingPoints->exists ? 'Save Changes' : 'Add Points' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    @if($currentGame->owners->count() > 0)
                                        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 overflow-hidden">
                                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                                <thead class="bg-neutral-100 dark:bg-neutral-700">
                                                    <tr>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Player</th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Points</th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Placement</th>
                                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Action</th>
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
                                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                                @if($gamePoint)
                                                                    <button wire:click="startEditingPoints({{ $gamePoint->id }})" class="px-2 py-1 bg-purple-700 text-white rounded hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors">Edit</button>
                                                                @else
                                                                    <button
                                                                        wire:click="addPointsToPlayer({{ $currentGame->id }}, {{ $owner->id }})"
                                                                        class="px-2 py-1 bg-green-700 text-white rounded hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors"
                                                                    >
                                                                        Add Points
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 p-3 bg-neutral-100 dark:bg-neutral-700 rounded-lg">Add players to the game to manage points.</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-neutral-100 dark:bg-neutral-800 p-5 rounded-lg text-center border border-neutral-300 dark:border-neutral-600">
                        <p class="text-neutral-700 dark:text-neutral-300 mb-3">No active game for this event.</p>
                        <button wire:click="startCreatingGame" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Start a Game
                        </button>
                    </div>
                @endif

                <!-- Upcoming Games Section -->
                @if($upcomingGames->count() > 0)
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50 mb-4">Upcoming Games</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($upcomingGames as $game)
                                <div class="bg-white dark:bg-neutral-800 border-l-4 border-amber-500 dark:border-amber-600 border-t border-r border-b border-neutral-300 dark:border-neutral-600 rounded-lg p-5 shadow-sm">
                                    @if($isEditingGame && $editingGame && $editingGame->id === $game->id)
                                    <!-- Upcoming Game Edit Form -->
                                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg mb-4 border border-amber-200 dark:border-amber-800">
                                        <h5 class="font-semibold text-neutral-900 dark:text-neutral-50 mb-3">Edit Upcoming Game</h5>
                                        <form wire:submit.prevent="saveGame">
                                            <div class="grid grid-cols-1 gap-4">
                                                <div>
                                                    <label for="gameName{{ $game->id }}" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Game Name</label>
                                                    <input type="text" id="gameName{{ $game->id }}" wire:model="gameName" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-amber-600 focus:ring-amber-600">
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="gameDurationHours{{ $game->id }}" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Hours</label>
                                                        <input type="number" id="gameDurationHours{{ $game->id }}" wire:model="gameDurationHours" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-amber-600 focus:ring-amber-600">
                                                    </div>
                                                    <div>
                                                        <label for="gameDurationMinutes{{ $game->id }}" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Minutes</label>
                                                        <input type="number" id="gameDurationMinutes{{ $game->id }}" wire:model="gameDurationMinutes" min="0" max="59" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-amber-600 focus:ring-amber-600">
                                                    </div>
                                                </div>

                                                <div class="flex justify-end space-x-3 mt-2">
                                                    <button type="button" wire:click="cancelEditGame" class="inline-flex items-center px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-amber-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 transition-colors">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex justify-between items-start">
                                        <h5 class="font-semibold text-neutral-900 dark:text-neutral-50 text-base">{{ $game->name }}</h5>
                                        <button wire:click="startEditingGame({{ $game->id }})" class="p-1.5 bg-amber-700 text-white rounded-md hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
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
                                        @if($isEditingGame && $editingGame && $editingGame->id === $game->id)
                                            <tr class="bg-blue-50 dark:bg-blue-900/30">
                                                <td colspan="4" class="px-6 py-4">
                                                    <form wire:submit.prevent="saveGame">
                                                        <div class="grid grid-cols-1 gap-4">
                                                            <div>
                                                                <label for="finishedGameName{{ $game->id }}" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Game Name</label>
                                                                <input type="text" id="finishedGameName{{ $game->id }}" wire:model="gameName" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                                @error('gameName') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label for="finishedGameDurationHours{{ $game->id }}" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Duration (Hours)</label>
                                                                    <input type="number" id="finishedGameDurationHours{{ $game->id }}" wire:model="gameDurationHours" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                                    @error('gameDurationHours') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                                                </div>
                                                                <div>
                                                                    <label for="finishedGameDurationMinutes{{ $game->id }}" class="block text-sm font-medium text-neutral-900 dark:text-neutral-100">Duration (Minutes)</label>
                                                                    <input type="number" id="finishedGameDurationMinutes{{ $game->id }}" wire:model="gameDurationMinutes" min="0" max="59" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                                    @error('gameDurationMinutes') <span class="text-red-700 text-xs mt-1">{{ $message }}</span> @enderror
                                                                </div>
                                                            </div>

                                                            <div class="flex justify-end space-x-3 mt-2">
                                                                <button type="button" wire:click="cancelEditGame" class="inline-flex items-center px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md font-medium text-sm text-neutral-800 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">
                                                                    Cancel
                                                                </button>
                                                                <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">
                                                                    Save
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @else
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
                                                    <button wire:click="startEditingGame({{ $game->id }})" class="px-3 py-1 bg-blue-700 text-white rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">Edit</button>
                                                </td>
                                            </tr>
                                        @endif
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
