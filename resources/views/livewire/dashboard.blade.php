<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Live Dashboard</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Real-time updates from your current game night.</p>
    </div>

    {{-- Now Playing --}}
    @if($activeEvent && $currentGame)
        <section class="mb-10">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                {{-- Active Banner --}}
                <div class="bg-emerald-600 dark:bg-emerald-700 px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-white"></span>
                        </span>
                        <span class="text-sm font-semibold text-white">Now Playing</span>
                    </div>
                    <span class="text-xs font-medium text-emerald-100 bg-emerald-700 dark:bg-emerald-800 rounded-full px-3 py-1">
                        {{ $gameDuration }}
                    </span>
                </div>

                {{-- Game Details --}}
                <div class="bg-white dark:bg-zinc-800 p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $currentGame->name }}</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $activeEvent->name }}</p>
                        </div>
                        <flux:badge color="green" variant="pill">Active</flux:badge>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-900 p-4">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Duration</p>
                            <p class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $currentGame->getDurationForHumans() }}</p>
                        </div>
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-900 p-4">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Players</p>
                            <p class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $currentGame->owners->count() }}</p>
                        </div>
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-900 p-4">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Started</p>
                            <p class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $activeEvent->started_at ? $activeEvent->started_at->format('g:i A') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if($currentGame->owners->count() > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($currentGame->owners as $owner)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 dark:bg-zinc-700 px-3 py-1 text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $owner->display_name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @else
        <section class="mb-10">
            <div class="rounded-xl border border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800/50 p-10 text-center">
                <flux:icon.puzzle-piece class="mx-auto h-10 w-10 text-zinc-400 dark:text-zinc-500" />
                <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-zinc-100">No active game</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">There are no games being played right now. Check back during your next game night.</p>
            </div>
        </section>
    @endif

    {{-- Up Next + Previously Played --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Upcoming Games --}}
        <section>
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                Up Next
            </h2>

            @if($upcomingGames->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingGames as $game)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $game->name }}</h3>
                                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $game->getDurationForHumans() }}</p>
                                </div>
                                <flux:badge color="yellow" variant="pill" size="sm">Upcoming</flux:badge>
                            </div>
                            @if($game->owners->count() > 0)
                                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    Owned by {{ $game->owners->pluck('display_name')->implode(', ') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 p-6 text-center">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No upcoming games scheduled.</p>
                </div>
            @endif
        </section>

        {{-- Previously Played --}}
        <section>
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                Previously Played
            </h2>

            @if($finishedGames->count() > 0)
                <div class="space-y-3">
                    @foreach($finishedGames as $game)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $game->name }}</h3>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $game->getDurationForHumans() }}</span>
                            </div>
                            @if($game->owners->count() > 0)
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $game->owners->pluck('display_name')->implode(', ') }} &middot; {{ $game->owners->count() }} {{ Str::plural('player', $game->owners->count()) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 p-6 text-center">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No finished games yet.</p>
                </div>
            @endif
        </section>
    </div>
</div>
