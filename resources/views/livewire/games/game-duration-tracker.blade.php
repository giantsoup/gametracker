<div wire:poll.30s="refresh" class="mt-3 space-y-2">
    @if($game->status === \App\Enums\GameStatus::Playing && $game->current_session_started_at)
        <div class="space-y-2">
            <!-- Duration Header with Time Information -->
            <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400 p-2 min-h-[44px]">
                <span class="font-medium">Game Duration</span>
                <span>{{ $currentDuration }} min / {{ $game->duration }} min</span>
            </div>

            <!-- Progress Bar -->
            @php
                $progressColor = $isOvertime ? 'bg-red-500' : ($percentComplete < 75 ? 'bg-blue-500' : 'bg-yellow-500');
            @endphp

            <div class="h-3 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                <div class="{{ $progressColor }} h-3 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $percentComplete }}%"></div>
            </div>

            <!-- Time Information -->
            <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 px-1">
                @if($startTime)
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Started: {{ $startTime }}</span>
                    </div>
                @endif

                @if($estimatedEndTime)
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Est. End: {{ $estimatedEndTime }}</span>
                    </div>
                @endif
            </div>

            <!-- Overtime Warning -->
            @if($isOvertime)
                <div class="mt-1 rounded-md bg-red-100 p-2 text-sm text-red-800 dark:bg-red-900/20 dark:text-red-300">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Game is running longer than estimated</span>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
