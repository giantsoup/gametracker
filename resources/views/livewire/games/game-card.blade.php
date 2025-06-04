<div
    class="game-card rounded-lg border border-neutral-200 bg-white p-4 sm:p-5 md:p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
    data-game-id="{{ $game->id }}"
    data-status="{{ $game->status->value }}"
    role="region"
    aria-labelledby="game-title-{{ $game->id }}"
>
    <div class="flex flex-col space-y-4 sm:space-y-5">
        <!-- Game Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <h4 id="game-title-{{ $game->id }}" class="text-base sm:text-lg md:text-xl font-medium text-neutral-900 dark:text-white">{{ $game->name }}</h4>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Reorder Controls (for Ready to Start games) -->
                @if($game->status === \App\Enums\GameStatus::Ready)
                    @include('livewire.games.game-reorder-controls', ['game' => $game])
                @endif

                <!-- Player Count Badge -->
                <span class="inline-flex items-center rounded-full bg-neutral-100 px-2 sm:px-3 py-1 sm:py-1.5 text-sm font-medium text-neutral-800 dark:bg-neutral-900/20 dark:text-neutral-300 min-h-[36px] sm:min-h-[44px]" aria-label="{{ $game->activePlayers->count() }} active players">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 sm:mr-1.5 h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" role="img">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>{{ $game->activePlayers->count() }}</span>
                </span>
            </div>
        </div>

        <!-- Game Details -->
        <div class="text-sm sm:text-base text-neutral-500 dark:text-neutral-400">
            <!-- Duration -->
            <div class="flex items-center" aria-label="Game duration: {{ $game->getDurationForHumans() }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 sm:mr-1.5 h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" role="img">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $game->getDurationForHumans() }}</span>
            </div>

            <!-- Owners -->
            @if($game->owners->isNotEmpty())
                <div class="mt-1 flex items-start" aria-label="Game owners: {{ $game->owners->pluck('name')->join(', ') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 sm:mr-1.5 h-4 w-4 sm:h-5 sm:w-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" role="img">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <div class="flex flex-wrap gap-1" role="list">
                        @foreach($game->owners as $owner)
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 sm:px-2.5 sm:py-1 text-xs sm:text-sm font-medium text-blue-800 dark:bg-blue-900/20 dark:text-blue-300" role="listitem">
                                {{ $owner->getDisplayName() }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Game Duration Tracker (for currently playing games) -->
        @if($game->status === \App\Enums\GameStatus::Playing)
            @livewire('games.game-duration-tracker', ['game' => $game], key('game-duration-' . $game->id))
        @endif

        <!-- Game Scores Display (for finished games) -->
        @if($game->status === \App\Enums\GameStatus::Finished)
            @include('livewire.games.game-scores-display', ['game' => $game])

            <!-- Assign Points Button -->
            <div class="mt-3 sm:mt-4 flex justify-center">
                <a
                    href="{{ route('games.points.wizard', $game) }}"
                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-600 min-h-[40px] sm:min-h-[44px] w-full sm:w-auto justify-center"
                    role="button"
                    aria-label="Assign points for {{ $game->name }}"
                    tabindex="0"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 sm:mr-3 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" role="img">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Assign Points</span>
                </a>
            </div>

            <!-- Quick Start Next Game Component -->
            @livewire('games.quick-start-next-game', ['game' => $game], key('quick-start-' . $game->id))
        @endif

        <!-- Game Players List -->
        <div class="mt-2 sm:mt-3 border-t border-neutral-200 pt-2 sm:pt-3 dark:border-neutral-700">
            @livewire('games.game-players-list', ['game' => $game], key('game-players-' . $game->id))
        </div>

        <!-- Game Status Manager -->
        <div class="mt-2">
            @livewire('games.game-status-manager', ['game' => $game], key('game-status-' . $game->id))
        </div>
    </div>
</div>
