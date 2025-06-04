<div class="mt-3">
    @php
        $gamePoints = \App\Models\GamePoint::where('game_id', $game->id)
            ->with('player')
            ->orderBy('placement', 'asc')
            ->orderBy('points', 'desc')
            ->get();
    @endphp

    @if($gamePoints->isEmpty())
        <div class="text-sm text-neutral-500 dark:text-neutral-400 p-2">
            <span>No scores recorded yet</span>
        </div>
    @else
        <div class="space-y-2">
            <div class="text-sm font-medium text-neutral-700 dark:text-neutral-300 p-2">Final Scores:</div>
            <div class="space-y-2">
                @foreach($gamePoints as $point)
                    <div class="flex items-center justify-between text-sm p-2 rounded-md hover:bg-neutral-50 dark:hover:bg-neutral-700/50 min-h-[44px]">
                        <div class="flex items-center">
                            @if($point->placement === 1)
                                <span class="mr-2 text-yellow-500 dark:text-yellow-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </span>
                            @endif
                            <span class="{{ $point->placement === 1 ? 'font-medium text-neutral-800 dark:text-neutral-200' : 'text-neutral-600 dark:text-neutral-400' }}">
                                {{ $point->player->name }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            @if($point->placement)
                                <span class="mr-3 inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                    {{ $point->placement }}{{ __('st') }}
                                </span>
                            @endif
                            <span class="{{ $point->placement === 1 ? 'font-medium text-neutral-800 dark:text-neutral-200' : 'text-neutral-600 dark:text-neutral-400' }}">
                                {{ $point->points }} pts
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
