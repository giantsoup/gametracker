<div class="flex space-x-2" role="group" aria-label="Game order controls">
    <flux:button
        wire:click="moveGameUp({{ $game->id }})"
        size="lg"
        variant="outline"
        class="border-neutral-300 bg-neutral-50 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/30 dark:text-neutral-300 dark:hover:bg-neutral-900/30 min-h-[44px] min-w-[44px] flex items-center justify-center"
        aria-label="Move {{ $game->name }} up in queue"
        tabindex="0"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" role="img">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </flux:button>

    <flux:button
        wire:click="moveGameDown({{ $game->id }})"
        size="lg"
        variant="outline"
        class="border-neutral-300 bg-neutral-50 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/30 dark:text-neutral-300 dark:hover:bg-neutral-900/30 min-h-[44px] min-w-[44px] flex items-center justify-center"
        aria-label="Move {{ $game->name }} down in queue"
        tabindex="0"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" role="img">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </flux:button>
</div>
