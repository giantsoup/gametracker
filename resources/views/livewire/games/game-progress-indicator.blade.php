<div class="mt-3">
    @if($game->isRunning())
        <div class="space-y-2">
            <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400 p-2 min-h-[44px]">
                <span class="font-medium">Progress</span>
                <span>{{ $game->getCurrentSessionDuration() }} min / {{ $game->duration }} min</span>
            </div>

            @php
                $progressPercent = min(100, round(($game->getCurrentSessionDuration() / max(1, $game->duration)) * 100));
                $progressColor = $progressPercent < 75 ? 'bg-blue-500' : ($progressPercent < 100 ? 'bg-yellow-500' : 'bg-red-500');
            @endphp

            <div class="h-3 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                <div class="{{ $progressColor }} h-3 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>
    @endif
</div>
