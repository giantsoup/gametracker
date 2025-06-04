<div wire:poll.10s
    x-data="{
        showGlobalNotification: {{ session()->has('message') ? 'true' : 'false' }},
        globalNotificationType: '{{ session('message-type', 'success') }}',
        globalNotificationMessage: '{{ session('message') }}'
    }"
    x-init="
        Alpine.store('pointsData', {
            pointsUpdated: null
        });

        // Auto-hide the notification after 5 seconds
        if (showGlobalNotification) {
            setTimeout(() => {
                showGlobalNotification = false;
            }, 5000);
        }
    "
>
    <!-- Global Notification Banner -->
    <div
        x-show="showGlobalNotification"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="mb-4 rounded-md p-4 fixed top-4 right-4 z-50 max-w-md shadow-lg"
        :class="globalNotificationType === 'success' ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800'"
    >
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg x-show="globalNotificationType === 'success'" class="h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <!-- Error Icon -->
                <svg x-show="globalNotificationType === 'error'" class="h-5 w-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium" :class="globalNotificationType === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'" x-text="globalNotificationMessage"></p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        @click="showGlobalNotification = false"
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="globalNotificationType === 'success' ? 'text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 focus:ring-green-600' : 'text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 focus:ring-red-600'"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Accessibility Styles -->
    <style>
        /* Focus styles for better keyboard navigation */
        *:focus-visible {
            outline: 2px solid #4338ca; /* Indigo-700 */
            outline-offset: 2px;
        }

        /* Animation for updated rows */
        @keyframes highlight-pulse {
            0% { background-color: transparent; }
            50% { background-color: rgba(167, 139, 250, 0.3); }
            100% { background-color: transparent; }
        }
        .highlight-animation {
            animation: highlight-pulse 2s ease-in-out;
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
                            <!-- Game Header with Name and Status -->
                            <div class="mb-4">
                                <div class="bg-indigo-50 dark:bg-indigo-900/20 px-4 py-3 rounded-lg border-l-4 border-indigo-600 dark:border-indigo-500">
                                    <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
                                        </svg>
                                        {{ $currentGame->name }}
                                    </h2>
                                    <div class="flex items-center mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $currentGame->getStatusColor() }}-100 text-{{ $currentGame->getStatusColor() }}-800 dark:bg-{{ $currentGame->getStatusColor() }}-800 dark:text-{{ $currentGame->getStatusColor() }}-100 mr-2">
                                            {{ $currentGame->getStatusLabel() }}
                                        </span>
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300">Duration: {{ $gameDuration }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Points Overview Card - Moved up for better visibility -->
                            <div class="mb-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg border border-purple-200 dark:border-purple-800 overflow-hidden">
                                <div class="px-4 py-3 bg-purple-100 dark:bg-purple-900/30 border-b border-purple-200 dark:border-purple-800">
                                    <div class="flex items-center justify-between">
                                        <h6 class="font-semibold text-purple-900 dark:text-purple-100 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-purple-700 dark:text-purple-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                            </svg>
                                            Points Distribution Overview
                                        </h6>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                            {{ $currentGame->total_points ?? 15 }} Total Points
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                        @php
                                            // Get points distribution (either from game or default)
                                            $distribution = $currentGame->points_distribution ?? \App\Models\Game::getDefaultPointsDistribution();
                                            // Sort by placement (key)
                                            ksort($distribution);
                                            // Get max points for scaling
                                            $maxPoints = !empty($distribution) ? max($distribution) : 1;
                                        @endphp

                                        @foreach($distribution as $placement => $points)
                                            @php
                                                // Calculate percentage for bar height (minimum 15%)
                                                $percentage = max(15, ($points / $maxPoints) * 100);

                                                // Determine color based on placement
                                                $colors = [
                                                    1 => 'bg-yellow-400 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100',
                                                    2 => 'bg-gray-300 dark:bg-gray-500 text-gray-900 dark:text-gray-100',
                                                    3 => 'bg-amber-600 dark:bg-amber-700 text-amber-100',
                                                    4 => 'bg-purple-500 dark:bg-purple-600 text-purple-100',
                                                    5 => 'bg-indigo-500 dark:bg-indigo-600 text-indigo-100',
                                                ];

                                                $color = $colors[$placement] ?? 'bg-blue-500 dark:bg-blue-600 text-blue-100';

                                                // Suffix for placement
                                                $suffix = $placement === 1 ? 'st' : ($placement === 2 ? 'nd' : ($placement === 3 ? 'rd' : 'th'));
                                            @endphp

                                            <div class="flex flex-col items-center" x-data="{ showTooltip: false }">
                                                <div class="relative w-full h-24 flex items-end justify-center mb-1"
                                                     @mouseenter="showTooltip = true"
                                                     @mouseleave="showTooltip = false">
                                                    <div class="absolute inset-0 flex items-center justify-center" x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" style="display: none;">
                                                        <div class="bg-black/80 text-white text-xs rounded py-1 px-2 pointer-events-none">
                                                            {{ $placement }}{{ $suffix }} place gets {{ $points }} points
                                                        </div>
                                                    </div>
                                                    <div class="w-12 {{ $color }} rounded-t-md" style="height: {{ $percentage }}%;"></div>
                                                </div>
                                                <div class="text-xs font-medium text-neutral-900 dark:text-neutral-100">{{ $placement }}{{ $suffix }}</div>
                                                <div class="text-sm font-bold text-purple-800 dark:text-purple-300">{{ $points }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 text-xs text-neutral-600 dark:text-neutral-400 italic">
                                        <span class="inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-purple-600 dark:text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            Hover over bars to see details. Points are distributed based on player placement.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Game Controls Card - Moved to a less prominent position -->
                            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Quick Actions Card -->
                                <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 p-4">
                                    <h6 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        </svg>
                                        Game Controls
                                    </h6>
                                    <div class="flex flex-wrap gap-2">
                                        @if($currentGame->isRunning())
                                            <button wire:click="stopGame({{ $currentGame->id }})" class="inline-flex items-center px-3 py-2 bg-red-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
                                                </svg>
                                                Stop Game
                                            </button>
                                        @else
                                            <button wire:click="startGame({{ $currentGame->id }})" class="inline-flex items-center px-3 py-2 bg-green-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                                Start Game
                                            </button>
                                        @endif
                                        <button wire:click="startEditingGame({{ $currentGame->id }})" class="inline-flex items-center px-3 py-2 bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Edit Game
                                        </button>
                                        <button
                                            wire:click="quickAssignPoints({{ $currentGame->id }})"
                                            class="inline-flex items-center px-3 py-2 bg-purple-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-75"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Quick Assign Points
                                        </button>
                                    </div>
                                </div>

                                <!-- Game Status Card -->
                                <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 p-4">
                                    <h6 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        Game Status
                                    </h6>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <button
                                            wire:click="setGameStatus({{ $currentGame->id }}, 'unplayed')"
                                            class="px-3 py-1 text-xs font-medium rounded-md {{ $currentGame->status && $currentGame->status->value === 'unplayed' ? 'bg-zinc-700 text-white' : 'bg-zinc-200 text-zinc-800 hover:bg-zinc-300' }}"
                                        >
                                            Unplayed
                                        </button>
                                        <button
                                            wire:click="setGameStatus({{ $currentGame->id }}, 'active')"
                                            class="px-3 py-1 text-xs font-medium rounded-md {{ $currentGame->status && $currentGame->status->value === 'active' ? 'bg-green-700 text-white' : 'bg-green-200 text-green-800 hover:bg-green-300' }}"
                                        >
                                            Active
                                        </button>
                                        <button
                                            wire:click="setGameStatus({{ $currentGame->id }}, 'played')"
                                            class="px-3 py-1 text-xs font-medium rounded-md {{ $currentGame->status && $currentGame->status->value === 'played' ? 'bg-blue-700 text-white' : 'bg-blue-200 text-blue-800 hover:bg-blue-300' }}"
                                        >
                                            Played
                                        </button>
                                    </div>
                                    <button
                                        wire:click="finalizeGame({{ $currentGame->id }})"
                                        class="w-full flex justify-center items-center px-3 py-2 bg-blue-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-75"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Finalize Game & Assign Points
                                    </button>
                                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400 text-center">
                                        This will mark the game as played and automatically assign points based on player order.
                                    </p>
                                </div>
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

                            <!-- Game Owners Section -->
                            <div class="mt-3">
                                <h6 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50 mb-2">Game Owners:</h6>
                                <div class="flex flex-wrap gap-2">
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
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300">No owners assigned to this game.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Game Players Section -->
                            <div class="mt-4">
                                <h6 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50 mb-2">Players:</h6>

                                @php
                                    // Get all players from the event
                                    $eventPlayers = $activeEvent->players;

                                    // Separate players by status
                                    $activePlayers = collect();
                                    $leftPlayers = collect();
                                    $notPlayingPlayers = collect();

                                    foreach ($eventPlayers as $player) {
                                        $statusKey = 'status_in_game_' . $currentGame->id;
                                        $playerStatus = $player->$statusKey;

                                        if ($playerStatus === 'playing') {
                                            $activePlayers->push($player);
                                        } elseif ($playerStatus === 'left') {
                                            $leftPlayers->push($player);
                                        } else {
                                            $notPlayingPlayers->push($player);
                                        }
                                    }
                                @endphp

                                <!-- Active Players Table -->
                                <div class="mb-4">
                                    <h6 class="text-sm font-medium text-neutral-900 dark:text-neutral-50 mb-2 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-green-600 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Active Players
                                    </h6>
                                    <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 overflow-hidden">
                                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                            <thead class="bg-neutral-100 dark:bg-neutral-700">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Player</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Status</th>
                                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                                                @forelse($activePlayers as $player)
                                                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                            {{ $player->display_name }}
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                                Playing
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                            <button
                                                                wire:click="markPlayerLeftGame({{ $currentGame->id }}, {{ $player->id }})"
                                                                class="px-2 py-1 bg-red-700 text-white rounded hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors"
                                                            >
                                                                Mark as Left
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="px-4 py-3 text-sm text-neutral-700 dark:text-neutral-300 text-center">
                                                            No active players in this game.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Players Who Left Table -->
                                @if($leftPlayers->isNotEmpty())
                                    <div class="mb-4">
                                        <h6 class="text-sm font-medium text-neutral-900 dark:text-neutral-50 mb-2 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Players Who Left
                                        </h6>
                                        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 overflow-hidden">
                                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                                <thead class="bg-neutral-100 dark:bg-neutral-700">
                                                    <tr>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Player</th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Status</th>
                                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                                                    @foreach($leftPlayers as $player)
                                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                                {{ $player->display_name }}
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                                    Left
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                                <button
                                                                    wire:click="markPlayerActiveInGame({{ $currentGame->id }}, {{ $player->id }})"
                                                                    class="px-2 py-1 bg-green-700 text-white rounded hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors"
                                                                >
                                                                    Mark as Playing
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <!-- Not Playing Players Table -->
                                @if($notPlayingPlayers->isNotEmpty())
                                    <div>
                                        <h6 class="text-sm font-medium text-neutral-900 dark:text-neutral-50 mb-2 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-neutral-600 dark:text-neutral-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                            </svg>
                                            Available Players
                                        </h6>
                                        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 overflow-hidden">
                                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                                                <thead class="bg-neutral-100 dark:bg-neutral-700">
                                                    <tr>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Player</th>
                                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Status</th>
                                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-neutral-900 dark:text-neutral-100 uppercase tracking-wider">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                                                    @foreach($notPlayingPlayers as $player)
                                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                                {{ $player->display_name }}
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                                    Not Playing
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                                <button
                                                                    wire:click="addPlayerDirectlyToGame({{ $currentGame->id }}, {{ $player->id }})"
                                                                    class="px-2 py-1 bg-blue-700 text-white rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors"
                                                                >
                                                                    Add to Game
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                @if($activePlayers->isEmpty() && $leftPlayers->isEmpty() && $notPlayingPlayers->isEmpty())
                                    <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-300 dark:border-neutral-600 overflow-hidden">
                                        <div class="px-4 py-3 text-sm text-neutral-700 dark:text-neutral-300 text-center">
                                            No additional players in this event.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Points Management Section -->
                            <div class="mt-6" x-data="{ showNotification: false, notificationType: 'success', notificationMessage: '' }">
                                <!-- Notification Banner -->
                                <div
                                    x-show="showNotification"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                                    class="mb-4 rounded-md p-4"
                                    :class="notificationType === 'success' ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800'"
                                >
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <!-- Success Icon -->
                                            <svg x-show="notificationType === 'success'" class="h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <!-- Error Icon -->
                                            <svg x-show="notificationType === 'error'" class="h-5 w-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium" :class="notificationType === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'" x-text="notificationMessage"></p>
                                        </div>
                                        <div class="ml-auto pl-3">
                                            <div class="-mx-1.5 -my-1.5">
                                                <button
                                                    @click="showNotification = false"
                                                    class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                                    :class="notificationType === 'success' ? 'text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 focus:ring-green-600' : 'text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 focus:ring-red-600'"
                                                >
                                                    <span class="sr-only">Dismiss</span>
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="text-base font-semibold text-neutral-900 dark:text-neutral-50">Points Management:</h5>
                                    <div class="flex space-x-2">
                                        <button
                                            wire:click="quickAssignPoints({{ $currentGame->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-purple-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-75"
                                            x-on:click="setTimeout(() => {
                                                showNotification = true;
                                                notificationType = 'success';
                                                notificationMessage = 'Points assigned successfully!';

                                                // Highlight all player rows one by one with a slight delay
                                                @foreach($currentGame->owners as $index => $player)
                                                    setTimeout(() => {
                                                        Alpine.store('pointsData').pointsUpdated = {
                                                            playerId: {{ $player->id }},
                                                            timestamp: Date.now()
                                                        };
                                                    }, {{ $index * 200 + 1000 }});
                                                @endforeach
                                            }, 1000)"
                                        >
                                            <svg wire:loading.remove wire:target="quickAssignPoints" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <svg wire:loading wire:target="quickAssignPoints" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Quick Assign Points
                                        </button>
                                    </div>
                                </div>

                                <!-- Points Overview Card -->
                                <div class="mb-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg border border-purple-200 dark:border-purple-800 overflow-hidden">
                                    <div class="px-4 py-3 bg-purple-100 dark:bg-purple-900/30 border-b border-purple-200 dark:border-purple-800">
                                        <div class="flex items-center justify-between">
                                            <h6 class="font-semibold text-purple-900 dark:text-purple-100 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-purple-700 dark:text-purple-300" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                                </svg>
                                                Points Distribution Overview
                                            </h6>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                                {{ $currentGame->total_points ?? 15 }} Total Points
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                            @php
                                                // Get points distribution (either from game or default)
                                                $distribution = $currentGame->points_distribution ?? \App\Models\Game::getDefaultPointsDistribution();
                                                // Sort by placement (key)
                                                ksort($distribution);
                                                // Get max points for scaling
                                                $maxPoints = !empty($distribution) ? max($distribution) : 1;
                                            @endphp

                                            @foreach($distribution as $placement => $points)
                                                @php
                                                    // Calculate percentage for bar height (minimum 15%)
                                                    $percentage = max(15, ($points / $maxPoints) * 100);

                                                    // Determine color based on placement
                                                    $colors = [
                                                        1 => 'bg-yellow-400 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100',
                                                        2 => 'bg-gray-300 dark:bg-gray-500 text-gray-900 dark:text-gray-100',
                                                        3 => 'bg-amber-600 dark:bg-amber-700 text-amber-100',
                                                        4 => 'bg-purple-500 dark:bg-purple-600 text-purple-100',
                                                        5 => 'bg-indigo-500 dark:bg-indigo-600 text-indigo-100',
                                                    ];

                                                    $color = $colors[$placement] ?? 'bg-blue-500 dark:bg-blue-600 text-blue-100';

                                                    // Suffix for placement
                                                    $suffix = $placement === 1 ? 'st' : ($placement === 2 ? 'nd' : ($placement === 3 ? 'rd' : 'th'));
                                                @endphp

                                                <div class="flex flex-col items-center" x-data="{ showTooltip: false }">
                                                    <div class="relative w-full h-24 flex items-end justify-center mb-1"
                                                         @mouseenter="showTooltip = true"
                                                         @mouseleave="showTooltip = false">
                                                        <div class="absolute inset-0 flex items-center justify-center" x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" style="display: none;">
                                                            <div class="bg-black/80 text-white text-xs rounded py-1 px-2 pointer-events-none">
                                                                {{ $placement }}{{ $suffix }} place gets {{ $points }} points
                                                            </div>
                                                        </div>
                                                        <div class="w-12 {{ $color }} rounded-t-md" style="height: {{ $percentage }}%;"></div>
                                                    </div>
                                                    <div class="text-xs font-medium text-neutral-900 dark:text-neutral-100">{{ $placement }}{{ $suffix }}</div>
                                                    <div class="text-sm font-bold text-purple-800 dark:text-purple-300">{{ $points }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-3 text-xs text-neutral-600 dark:text-neutral-400 italic">
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-purple-600 dark:text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Hover over bars to see details. Points are distributed based on player placement.
                                            </span>
                                        </div>
                                    </div>
                                </div>

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
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center px-3 py-2 bg-purple-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="opacity-75"
                                                        wire:target="savePoints"
                                                        x-on:click="setTimeout(() => {
                                                            showNotification = true;
                                                            notificationType = 'success';
                                                            notificationMessage = 'Points saved successfully!';
                                                            Alpine.store('pointsData').pointsUpdated = {
                                                                playerId: {{ $editingPoints->player_id ?? 0 }},
                                                                timestamp: Date.now()
                                                            };
                                                        }, 1000)"
                                                    >
                                                        <span wire:loading.remove wire:target="savePoints">
                                                            {{ $editingPoints->exists ? 'Save Changes' : 'Add Points' }}
                                                        </span>
                                                        <span wire:loading wire:target="savePoints" class="inline-flex items-center">
                                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            Saving...
                                                        </span>
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
                                                        <tr
                                                            class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50"
                                                            x-data="{
                                                                highlighted: false,
                                                                init() {
                                                                    this.$nextTick(() => {
                                                                        this.$watch('$store.pointsUpdated', value => {
                                                                            if (value && value.playerId === {{ $owner->id }}) {
                                                                                this.highlighted = true;
                                                                                setTimeout(() => {
                                                                                    this.highlighted = false;
                                                                                }, 2000);
                                                                            }
                                                                        });
                                                                    });
                                                                }
                                                            }"
                                                            :class="{ 'bg-green-100 dark:bg-green-900/30': highlighted }"
                                                        >
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100 transition-colors duration-500" :class="{ 'text-green-800 dark:text-green-200': highlighted }">{{ $owner->display_name }}</td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300 transition-colors duration-500" :class="{ 'text-green-800 dark:text-green-200 font-medium': highlighted }">{{ $gamePoint ? $gamePoint->points : '-' }}</td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300 transition-colors duration-500" :class="{ 'text-green-800 dark:text-green-200 font-medium': highlighted }">{{ $gamePoint ? $gamePoint->placement : '-' }}</td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                                @if($gamePoint)
                                                                    <button
                                                                        wire:click="startEditingPoints({{ $gamePoint->id }})"
                                                                        class="px-2 py-1 bg-purple-700 text-white rounded hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-colors"
                                                                        wire:loading.attr="disabled"
                                                                        wire:loading.class="opacity-75"
                                                                        wire:target="startEditingPoints({{ $gamePoint->id }})"
                                                                    >
                                                                        <span wire:loading.remove wire:target="startEditingPoints({{ $gamePoint->id }})">Edit</span>
                                                                        <span wire:loading wire:target="startEditingPoints({{ $gamePoint->id }})" class="inline-flex items-center">
                                                                            <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                            </svg>
                                                                            Loading
                                                                        </span>
                                                                    </button>
                                                                @else
                                                                    <button
                                                                        wire:click="addPointsToPlayer({{ $currentGame->id }}, {{ $owner->id }})"
                                                                        class="px-2 py-1 bg-green-700 text-white rounded hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors"
                                                                        wire:loading.attr="disabled"
                                                                        wire:loading.class="opacity-75"
                                                                        wire:target="addPointsToPlayer({{ $currentGame->id }}, {{ $owner->id }})"
                                                                        x-on:click="setTimeout(() => { showNotification = true; notificationType = 'success'; notificationMessage = 'Points form opened for {{ $owner->display_name }}'; }, 500)"
                                                                    >
                                                                        <span wire:loading.remove wire:target="addPointsToPlayer({{ $currentGame->id }}, {{ $owner->id }})">Add Points</span>
                                                                        <span wire:loading wire:target="addPointsToPlayer({{ $currentGame->id }}, {{ $owner->id }})" class="inline-flex items-center">
                                                                            <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                            </svg>
                                                                            Loading
                                                                        </span>
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
                        <p class="text-neutral-700 dark:text-neutral-300">No active game for this event.</p>
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
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $game->getStatusColor() }}-100 text-{{ $game->getStatusColor() }}-800 dark:bg-{{ $game->getStatusColor() }}-800 dark:text-{{ $game->getStatusColor() }}-100">
                                                {{ $game->getStatusLabel() }}
                                            </span>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-amber-700 dark:text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                            </div>
                                        </div>

                                        <!-- Game Controls -->
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @if($game->isRunning())
                                                <button wire:click="stopGame({{ $game->id }})" class="inline-flex items-center px-2 py-1 bg-red-700 border border-transparent rounded-md font-medium text-xs text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors">
                                                    Stop Game
                                                </button>
                                            @else
                                                <button wire:click="startGame({{ $game->id }})" class="inline-flex items-center px-2 py-1 bg-green-700 border border-transparent rounded-md font-medium text-xs text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">
                                                    Start Game
                                                </button>
                                            @endif

                                            <button
                                                wire:click="setGameStatus({{ $game->id }}, 'unplayed')"
                                                class="px-2 py-1 text-xs font-medium rounded-md {{ $game->status && $game->status->value === 'unplayed' ? 'bg-zinc-700 text-white' : 'bg-zinc-200 text-zinc-800 hover:bg-zinc-300' }}"
                                            >
                                                Unplayed
                                            </button>
                                            <button
                                                wire:click="setGameStatus({{ $game->id }}, 'active')"
                                                class="px-2 py-1 text-xs font-medium rounded-md {{ $game->status && $game->status->value === 'active' ? 'bg-green-700 text-white' : 'bg-green-200 text-green-800 hover:bg-green-300' }}"
                                            >
                                                Active
                                            </button>
                                            <button
                                                wire:click="setGameStatus({{ $game->id }}, 'played')"
                                                class="px-2 py-1 text-xs font-medium rounded-md {{ $game->status && $game->status->value === 'played' ? 'bg-blue-700 text-white' : 'bg-blue-200 text-blue-800 hover:bg-blue-300' }}"
                                            >
                                                Played
                                            </button>
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
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Duration</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Players</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-900 dark:text-neutral-50 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                                    @foreach($finishedGames as $game)
                                        @if($isEditingGame && $editingGame && $editingGame->id === $game->id)
                                            <tr class="bg-blue-50 dark:bg-blue-900/30">
                                                <td colspan="5" class="px-6 py-4">
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
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                                                    <div class="relative" x-data="{ open: false }">
                                                        <button @click="open = !open" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $game->getStatusColor() }}-100 text-{{ $game->getStatusColor() }}-800 dark:bg-{{ $game->getStatusColor() }}-800 dark:text-{{ $game->getStatusColor() }}-100 hover:bg-{{ $game->getStatusColor() }}-200 dark:hover:bg-{{ $game->getStatusColor() }}-700 focus:outline-none">
                                                            {{ $game->getStatusLabel() }}
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-1 w-36 bg-white dark:bg-neutral-700 rounded-md shadow-lg">
                                                            <div class="py-1">
                                                                <button wire:click="setGameStatus({{ $game->id }}, 'unplayed')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-600">
                                                                    Unplayed
                                                                </button>
                                                                <button wire:click="setGameStatus({{ $game->id }}, 'active')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-600">
                                                                    Active
                                                                </button>
                                                                <button wire:click="setGameStatus({{ $game->id }}, 'played')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-600">
                                                                    Played
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
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
                                                    <div class="flex justify-end space-x-2">
                                                        @if($game->isRunning())
                                                            <button wire:click="stopGame({{ $game->id }})" class="px-2 py-1 bg-red-700 text-white rounded hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors">Stop</button>
                                                        @else
                                                            <button wire:click="startGame({{ $game->id }})" class="px-2 py-1 bg-green-700 text-white rounded hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors">Start</button>
                                                        @endif
                                                        <button wire:click="startEditingGame({{ $game->id }})" class="px-2 py-1 bg-blue-700 text-white rounded hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors">Edit</button>
                                                    </div>
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
