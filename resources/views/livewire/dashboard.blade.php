<style>
    /* Projection Mode Styles (1920x1080) */
    .projection-mode {
        font-size: 16px; /* Base font size increase for better readability on large screens */
    }

    .projection-mode .progress-bar {
        height: 12px !important; /* Larger progress bars for better visibility */
    }

    .projection-mode .card {
        transition: transform 0.3s ease;
    }

    .projection-mode .card:hover {
        transform: translateY(-5px);
    }

    /* Mobile Mode Styles */
    .mobile-mode {
        font-size: 14px; /* Slightly smaller base font for mobile */
    }

    .mobile-mode .touch-target {
        min-height: 44px; /* Ensure touch targets are at least 44px for mobile usability */
        min-width: 44px;
    }

    .mobile-mode .card {
        margin-bottom: 16px; /* Add more space between cards on mobile */
    }

    /* Animation for real-time updates - can be applied to elements that change */
    .highlight-update {
        animation: highlight 2s ease-in-out;
    }

    @keyframes highlight {
        0% { background-color: rgba(79, 70, 229, 0.2); }
        100% { background-color: transparent; }
    }

    /* Responsive font scaling */
    @media (max-width: 640px) {
        .mobile-mode h1 { font-size: 1.5rem; }
        .mobile-mode h2 { font-size: 1.25rem; }
        .mobile-mode h3 { font-size: 1.125rem; }
    }

    @media (min-width: 1920px) {
        .projection-mode h1 { font-size: 3rem; }
        .projection-mode h2 { font-size: 2.5rem; }
        .projection-mode h3 { font-size: 2rem; }
    }
</style>

<div class="relative" data-component="dashboard" wire:id="dashboard">
    @if(app()->environment('local', 'development', 'testing'))
        <x-js-debug />
    @endif
    <!-- GameTracker Logo/Title and Login/Dashboard Link at the top -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">GameTracker</h1>
            </div>

            <!-- Login/Dashboard Link -->
            <div class="flex items-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm">Login</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Floating Menu Button at bottom right -->
    <div class="fixed bottom-4 right-4 z-50">
        <!-- Floating Menu Toggle Button -->
        <button id="floating-menu-toggle" class="bg-indigo-600 dark:bg-indigo-700 text-white p-3 rounded-full shadow-lg hover:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>

        <!-- Floating Menu Panel -->
        <div id="floating-menu-panel" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 mb-2 w-64">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Display Options</h3>
                <button id="close-floating-menu" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Event Switcher -->
            @if($events->count() > 1)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Active Events:</label>
                <div class="space-y-2">
                    @foreach($events as $event)
                        <button
                            wire:click="switchEvent({{ $event->id }})"
                            class="w-full text-left px-3 py-2 rounded-md text-sm {{ $activeEvent && $activeEvent->id == $event->id ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 font-medium' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                        >
                            <div class="flex items-center">
                                @if($activeEvent && $activeEvent->id == $event->id)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                {{ $event->name }}
                            </div>
                            @if($event->started_at)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Started: {{ $event->started_at->format('M d, H:i') }}
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Display Type Switcher -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Display Type:</label>
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <!-- Using regular links with query parameters instead of Livewire events -->
                    <a
                        href="/?display=default{{ $activeLayout ? '&layout='.$activeLayout : '' }}{{ $activeEvent ? '&event='.$activeEvent->id : '' }}"
                        class="flex-1 p-1.5 rounded-md text-center {{ $displayType == 'default' ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }} {{ $displayType == 'mobile' ? 'touch-target' : '' }}"
                        title="Default Display"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a
                        href="/?display=projection{{ $activeLayout ? '&layout='.$activeLayout : '' }}{{ $activeEvent ? '&event='.$activeEvent->id : '' }}"
                        class="flex-1 p-1.5 rounded-md text-center {{ $displayType == 'projection' ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }} {{ $displayType == 'mobile' ? 'touch-target' : '' }}"
                        title="Projection Display (1920x1080)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a
                        href="/?display=mobile{{ $activeLayout ? '&layout='.$activeLayout : '' }}{{ $activeEvent ? '&event='.$activeEvent->id : '' }}"
                        class="flex-1 p-1.5 rounded-md text-center {{ $displayType == 'mobile' ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }} {{ $displayType == 'mobile' ? 'touch-target' : '' }}"
                        title="Mobile Display"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Layout Switcher -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Layout:</label>
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <!-- Using regular links with query parameters instead of Livewire events -->
                    <a
                        href="/?layout=1{{ $displayType != 'default' ? '&display='.$displayType : '' }}{{ $activeEvent ? '&event='.$activeEvent->id : '' }}"
                        class="flex-1 p-1.5 rounded-md text-center {{ $activeLayout == 1 ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }} {{ $displayType == 'mobile' ? 'touch-target' : '' }}"
                        title="Layout 1 - Focus on current game"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                        </svg>
                    </a>
                    <a
                        href="/?layout=2{{ $displayType != 'default' ? '&display='.$displayType : '' }}{{ $activeEvent ? '&event='.$activeEvent->id : '' }}"
                        class="flex-1 p-1.5 rounded-md text-center {{ $activeLayout == 2 ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }} {{ $displayType == 'mobile' ? 'touch-target' : '' }}"
                        title="Layout 2 - Split view"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </a>
                    <a
                        href="/?layout=3{{ $displayType != 'default' ? '&display='.$displayType : '' }}{{ $activeEvent ? '&event='.$activeEvent->id : '' }}"
                        class="flex-1 p-1.5 rounded-md text-center {{ $activeLayout == 3 ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }} {{ $displayType == 'mobile' ? 'touch-target' : '' }}"
                        title="Layout 3 - Card Grid"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div>

    <!-- Layout 1: Focus on current game with sidebar for upcoming events -->
    @if($activeLayout == 1)
    <div class="min-h-screen {{ $displayType == 'projection' ? 'projection-mode' : '' }}">
        <div class="{{ $displayType == 'projection' ? 'max-w-full px-8' : 'max-w-7xl mx-auto' }}">
            <div class="text-center mb-8">
                <h1 class="{{ $displayType == 'projection' ? 'text-6xl' : 'text-4xl' }} font-bold text-gray-900 dark:text-white">GameTracker Live Dashboard</h1>
                <p class="mt-2 {{ $displayType == 'projection' ? 'text-2xl' : 'text-lg' }} text-gray-600 dark:text-gray-300">Real-time updates of gaming events</p>
            </div>

            <div class="grid grid-cols-1 {{ $displayType == 'projection' ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-8">
                <!-- Main Content - Current Game -->
                <div class="{{ $displayType == 'projection' ? 'lg:col-span-3' : 'lg:col-span-2' }} bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    @if($activeEvent && $currentGame)
                        <div class="relative">
                            <!-- Game Header with Duration Badge -->
                            <div class="bg-indigo-600 dark:bg-indigo-800 {{ $displayType == 'projection' ? 'p-8' : 'p-6' }} relative">
                                <div class="absolute top-4 right-4 bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-300 px-4 py-2 rounded-full {{ $displayType == 'projection' ? 'text-xl' : 'text-sm' }} font-medium">
                                    Running for: {{ $gameDuration }}
                                </div>
                                <h2 class="{{ $displayType == 'projection' ? 'text-4xl' : 'text-2xl' }} font-bold text-white">Current Game</h2>
                                <div class="flex items-center">
                                    <p class="{{ $displayType == 'projection' ? 'text-xl' : '' }} text-indigo-100">{{ $activeEvent->name }}</p>
                                    @if($events->count() > 1)
                                        <span class="ml-2 bg-indigo-500 dark:bg-indigo-700 text-white px-2 py-0.5 rounded-full text-xs">
                                            {{ $events->count() }} active events
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Game Details -->
                            <div class="{{ $displayType == 'projection' ? 'p-8' : 'p-6' }}">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="{{ $displayType == 'projection' ? 'text-5xl' : 'text-3xl' }} font-bold text-gray-900 dark:text-white">{{ $currentGame->name }}</h3>
                                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 {{ $displayType == 'projection' ? 'px-5 py-2 text-xl' : 'px-3 py-1 text-sm' }} rounded-full font-medium">
                                        Active
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="bg-gray-50 dark:bg-gray-700 {{ $displayType == 'projection' ? 'p-6' : 'p-4' }} rounded-lg">
                                        <h4 class="{{ $displayType == 'projection' ? 'text-2xl' : 'text-lg' }} font-medium text-gray-900 dark:text-white mb-2">Duration</h4>
                                        <p class="{{ $displayType == 'projection' ? 'text-xl' : '' }} text-gray-700 dark:text-gray-300">{{ $currentGame->getDurationForHumans() }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 {{ $displayType == 'projection' ? 'p-6' : 'p-4' }} rounded-lg">
                                        <h4 class="{{ $displayType == 'projection' ? 'text-2xl' : 'text-lg' }} font-medium text-gray-900 dark:text-white mb-2">Players</h4>
                                        <p class="{{ $displayType == 'projection' ? 'text-xl' : '' }} text-gray-700 dark:text-gray-300">{{ $currentGame->owners->count() }} participating</p>
                                        @if($currentGame->owners->count() > 0)
                                            <div class="mt-2 {{ $displayType == 'projection' ? 'text-lg' : 'text-sm' }} text-gray-600 dark:text-gray-400">
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
                                        <h4 class="{{ $displayType == 'projection' ? 'text-2xl' : 'text-lg' }} font-medium text-gray-900 dark:text-white mb-2">Event Progress</h4>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full {{ $displayType == 'projection' ? 'h-4' : 'h-2.5' }} progress-bar">
                                            <div class="bg-indigo-600 dark:bg-indigo-500 {{ $displayType == 'projection' ? 'h-4' : 'h-2.5' }} rounded-full progress-bar" style="width: 45%"></div>
                                        </div>
                                        <div class="flex justify-between {{ $displayType == 'projection' ? 'text-lg' : 'text-sm' }} text-gray-600 dark:text-gray-400 mt-2">
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
                        <div class="{{ $displayType == 'projection' ? 'p-16' : 'p-12' }} text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $displayType == 'projection' ? 'h-24 w-24' : 'h-16 w-16' }} mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-4 {{ $displayType == 'projection' ? 'text-3xl' : 'text-xl' }} font-medium text-gray-900 dark:text-white">No Active Games</h3>
                            <p class="mt-2 {{ $displayType == 'projection' ? 'text-xl' : '' }} text-gray-600 dark:text-gray-400">There are no active games at the moment. Check back later or view upcoming events.</p>
                        </div>
                    @endif
                </div>

                <!-- Sidebar - Game Lists -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <!-- Upcoming Games Section - Prominently Displayed -->
                    <div class="bg-yellow-600 dark:bg-yellow-800 {{ $displayType == 'projection' ? 'p-6' : 'p-4' }}">
                        <h2 class="{{ $displayType == 'projection' ? 'text-3xl' : 'text-xl' }} font-bold text-white">Upcoming Games</h2>
                    </div>
                    <div class="{{ $displayType == 'projection' ? 'p-6' : 'p-5' }}">
                        @if($upcomingGames->count() > 0)
                            <div class="space-y-5">
                                @foreach($upcomingGames as $game)
                                    <div class="border-l-4 border-yellow-500 dark:border-yellow-600 pl-3 py-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-r-md {{ $displayType == 'projection' ? 'pl-4 py-3' : '' }}">
                                        <h3 class="{{ $displayType == 'projection' ? 'text-2xl' : 'text-lg' }} font-medium text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                        <div class="flex items-center {{ $displayType == 'projection' ? 'text-lg' : 'text-sm' }} text-gray-600 dark:text-gray-400 mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $displayType == 'projection' ? 'h-5 w-5' : 'h-4 w-4' }} mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                        </div>
                                        @if($game->owners->count() > 0)
                                            <div class="mt-2 {{ $displayType == 'projection' ? 'text-sm' : 'text-xs' }} text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Owned by:</span>
                                                {{ $game->owners->pluck('display_name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="{{ $displayType == 'projection' ? 'text-xl' : '' }} text-gray-600 dark:text-gray-400">No upcoming games scheduled.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Finished Games Section - Less Prominent -->
                    <div class="bg-blue-500 dark:bg-blue-700 {{ $displayType == 'projection' ? 'p-4' : 'p-3' }} border-t border-gray-200 dark:border-gray-700">
                        <h2 class="{{ $displayType == 'projection' ? 'text-xl' : 'text-base' }} font-bold text-white">Previously Played</h2>
                    </div>
                    <div class="{{ $displayType == 'projection' ? 'p-4' : 'p-3' }}">
                        @if($finishedGames->count() > 0)
                            <div class="grid grid-cols-1 {{ $displayType == 'projection' ? 'gap-4' : 'gap-3' }}">
                                @foreach($finishedGames as $game)
                                    <div class="border-t border-gray-200 dark:border-gray-700 {{ $displayType == 'projection' ? 'pt-3' : 'pt-2' }}">
                                        <h3 class="{{ $displayType == 'projection' ? 'text-lg' : 'text-sm' }} font-medium text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                        <div class="flex justify-between items-center {{ $displayType == 'projection' ? 'text-sm' : 'text-xs' }} text-gray-600 dark:text-gray-400 mt-1">
                                            <span>{{ $game->getDurationForHumans() }}</span>
                                            <span>{{ $game->owners->count() }} players</span>
                                        </div>
                                        @if($game->owners->count() > 0)
                                            <div class="{{ $displayType == 'projection' ? 'text-sm' : 'text-xs' }} text-gray-600 dark:text-gray-400 mt-1">
                                                <span class="font-medium">Owned by:</span>
                                                {{ $game->owners->pluck('display_name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-2">
                                <p class="{{ $displayType == 'projection' ? 'text-base' : 'text-sm' }} text-gray-600 dark:text-gray-400">No finished games yet.</p>
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
                                        <div class="flex items-center">
                                            <p class="text-gray-600 dark:text-gray-400">{{ $activeEvent->name }}</p>
                                            @if($events->count() > 1)
                                                <span class="ml-2 bg-indigo-500 dark:bg-indigo-700 text-white px-2 py-0.5 rounded-full text-xs">
                                                    {{ $events->count() }} active events
                                                </span>
                                            @endif
                                        </div>
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

    <!-- Layout 3: Card Grid with emphasis on upcoming games - Optimized for mobile -->
    @if($activeLayout == 3)
    <div class="min-h-screen {{ $displayType == 'mobile' ? 'mobile-mode' : '' }}">
        <div class="{{ $displayType == 'mobile' ? 'max-w-full px-4' : 'max-w-7xl mx-auto' }}">
            <div class="text-center {{ $displayType == 'mobile' ? 'mb-6' : 'mb-8' }}">
                <h1 class="{{ $displayType == 'mobile' ? 'text-3xl' : 'text-4xl' }} font-bold text-gray-900 dark:text-white">Game Cards</h1>
                <p class="mt-2 {{ $displayType == 'mobile' ? 'text-base' : 'text-lg' }} text-gray-600 dark:text-gray-300">View all games at a glance</p>
            </div>

            <!-- Current Game Section -->
            @if($activeEvent && $currentGame)
                <div class="{{ $displayType == 'mobile' ? 'mb-6' : 'mb-10' }}">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 border-green-500 dark:border-green-600">
                        <div class="bg-green-500 dark:bg-green-600 {{ $displayType == 'mobile' ? 'px-4 py-3' : 'px-6 py-4' }}">
                            <div class="flex justify-between items-center">
                                <h2 class="{{ $displayType == 'mobile' ? 'text-lg' : 'text-xl' }} font-bold text-white">Now Playing</h2>
                                <div class="bg-white dark:bg-gray-900 text-green-600 dark:text-green-400 {{ $displayType == 'mobile' ? 'px-2 py-1 text-xs' : 'px-3 py-1 text-sm' }} rounded-full font-medium">
                                    {{ $gameDuration }}
                                </div>
                            </div>
                        </div>

                        <div class="{{ $displayType == 'mobile' ? 'p-4' : 'p-6' }}">
                            <div class="flex flex-col {{ $displayType == 'mobile' ? '' : 'md:flex-row md:items-center md:justify-between' }}">
                                <div>
                                    <h3 class="{{ $displayType == 'mobile' ? 'text-xl' : 'text-2xl' }} font-bold text-gray-900 dark:text-white">{{ $currentGame->name }}</h3>
                                    <div class="flex items-center">
                                        <p class="{{ $displayType == 'mobile' ? 'text-sm' : '' }} text-gray-600 dark:text-gray-400">{{ $activeEvent->name }}</p>
                                        @if($events->count() > 1)
                                            <span class="ml-2 bg-indigo-500 dark:bg-indigo-700 text-white px-2 py-0.5 rounded-full text-xs">
                                                {{ $events->count() }} active events
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="{{ $displayType == 'mobile' ? 'mt-2' : 'mt-4 md:mt-0' }}">
                                    <div class="flex items-center {{ $displayType == 'mobile' ? '' : 'justify-end' }}">
                                        <span class="{{ $displayType == 'mobile' ? 'text-xs' : 'text-sm' }} text-gray-600 dark:text-gray-400 mr-2">{{ $currentGame->owners->count() }} players</span>
                                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 {{ $displayType == 'mobile' ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-sm' }} rounded-full font-medium">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="{{ $displayType == 'mobile' ? 'mt-4' : 'mt-6' }}">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mr-2">
                                        <div class="bg-green-500 dark:bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                                    </div>
                                    <span class="{{ $displayType == 'mobile' ? 'text-xs' : 'text-sm' }} text-gray-600 dark:text-gray-400 whitespace-nowrap">45%</span>
                                </div>

                                <div class="flex justify-between {{ $displayType == 'mobile' ? 'text-xs' : 'text-sm' }} text-gray-600 dark:text-gray-400 mt-1">
                                    <span>Started: {{ $activeEvent->started_at ? $activeEvent->started_at->format('H:i') : 'N/A' }}</span>
                                    <span>Duration: {{ $currentGame->getDurationForHumans() }}</span>
                                </div>

                                @if($currentGame->owners->count() > 0)
                                    <div class="{{ $displayType == 'mobile' ? 'mt-3 text-xs' : 'mt-4 text-sm' }} text-gray-600 dark:text-gray-400">
                                        <p class="font-medium">Owned by:</p>
                                        <p class="mt-1">{{ $currentGame->owners->pluck('display_name')->implode(', ') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="{{ $displayType == 'mobile' ? 'mb-6' : 'mb-10' }}">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg {{ $displayType == 'mobile' ? 'p-4' : 'p-6' }} text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $displayType == 'mobile' ? 'h-10 w-10' : 'h-12 w-12' }} mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 {{ $displayType == 'mobile' ? 'text-lg' : 'text-xl' }} font-medium text-gray-900 dark:text-white">No Active Games</h3>
                        <p class="mt-2 {{ $displayType == 'mobile' ? 'text-sm' : '' }} text-gray-600 dark:text-gray-400">There are no active games at the moment.</p>
                    </div>
                </div>
            @endif

            <!-- Upcoming Games Section - Prominently Displayed -->
            <div class="{{ $displayType == 'mobile' ? 'mb-8' : 'mb-12' }}">
                <h2 class="{{ $displayType == 'mobile' ? 'text-xl' : 'text-2xl' }} font-bold text-gray-900 dark:text-white {{ $displayType == 'mobile' ? 'mb-4' : 'mb-6' }} flex items-center">
                    <span class="inline-block {{ $displayType == 'mobile' ? 'w-3 h-3' : 'w-4 h-4' }} bg-yellow-500 rounded-full mr-2"></span>
                    Coming Up Next
                </h2>

                @if($upcomingGames->count() > 0)
                    <div class="grid grid-cols-1 {{ $displayType == 'mobile' ? 'gap-4' : 'md:grid-cols-2 lg:grid-cols-3 gap-6' }}">
                        @foreach($upcomingGames as $game)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-l-4 border-yellow-500 dark:border-yellow-600 {{ $displayType == 'mobile' ? 'card' : 'transform transition-all duration-300 hover:scale-105 hover:shadow-xl card' }}">
                                <div class="{{ $displayType == 'mobile' ? 'p-4' : 'p-6' }}">
                                    <div class="flex justify-between items-start {{ $displayType == 'mobile' ? 'mb-3' : 'mb-4' }}">
                                        <h3 class="{{ $displayType == 'mobile' ? 'text-lg' : 'text-xl' }} font-bold text-gray-900 dark:text-white">{{ $game->name }}</h3>
                                        <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 {{ $displayType == 'mobile' ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-sm' }} rounded-full font-medium">
                                            Upcoming
                                        </span>
                                    </div>

                                    <div class="{{ $displayType == 'mobile' ? 'mt-3' : 'mt-4' }}">
                                        <div class="flex items-center {{ $displayType == 'mobile' ? 'text-sm' : '' }} text-gray-600 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $displayType == 'mobile' ? 'h-4 w-4' : 'h-5 w-5' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Duration: {{ $game->getDurationForHumans() }}</span>
                                        </div>

                                        @if($game->owners->count() > 0)
                                            <div class="mt-2 {{ $displayType == 'mobile' ? 'text-xs' : 'text-sm' }} text-gray-600 dark:text-gray-400">
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg {{ $displayType == 'mobile' ? 'p-4' : 'p-6' }} text-center">
                        <p class="{{ $displayType == 'mobile' ? 'text-sm' : '' }} text-gray-600 dark:text-gray-400">No upcoming games scheduled.</p>
                    </div>
                @endif
            </div>

            <!-- Finished Games Section - Less Prominent -->
            <div>
                <h2 class="{{ $displayType == 'mobile' ? 'text-lg' : 'text-xl' }} font-bold text-gray-900 dark:text-white {{ $displayType == 'mobile' ? 'mb-3' : 'mb-4' }} flex items-center">
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                    Previously Played
                </h2>

                @if($finishedGames->count() > 0)
                    <div class="grid grid-cols-1 {{ $displayType == 'mobile' ? 'gap-3' : 'md:grid-cols-3 lg:grid-cols-4 gap-4' }}">
                        @foreach($finishedGames as $game)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border-t border-blue-300 dark:border-blue-800 card">
                                <div class="{{ $displayType == 'mobile' ? 'p-3' : 'p-4' }}">
                                    <h3 class="{{ $displayType == 'mobile' ? 'text-base' : 'text-lg' }} font-medium text-gray-900 dark:text-white mb-2">{{ $game->name }}</h3>

                                    <div class="flex justify-between items-center {{ $displayType == 'mobile' ? 'text-xs' : 'text-sm' }} text-gray-600 dark:text-gray-400">
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md {{ $displayType == 'mobile' ? 'p-3' : 'p-4' }} text-center">
                        <p class="{{ $displayType == 'mobile' ? 'text-xs' : '' }} text-gray-600 dark:text-gray-400">No finished games yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    </div>
</div>

<!-- JavaScript for Floating Menu Toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('floating-menu-toggle');
        const closeButton = document.getElementById('close-floating-menu');
        const menuPanel = document.getElementById('floating-menu-panel');

        // Toggle menu when clicking the settings button
        toggleButton.addEventListener('click', function() {
            menuPanel.classList.toggle('hidden');
        });

        // Close menu when clicking the close button
        closeButton.addEventListener('click', function() {
            menuPanel.classList.add('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = menuPanel.contains(event.target) || toggleButton.contains(event.target);
            if (!isClickInside && !menuPanel.classList.contains('hidden')) {
                menuPanel.classList.add('hidden');
            }
        });
    });
</script>
