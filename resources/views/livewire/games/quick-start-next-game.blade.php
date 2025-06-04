<div>
    @if($nextGame)
        <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800/30">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    <div>
                        <h3 class="text-base font-medium text-blue-800 dark:text-blue-300">Ready to start next game</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400">{{ $nextGame->name }} ({{ $nextGame->activePlayers->count() }} players)</p>
                    </div>
                </div>

                <flux:button
                    wire:click="startNextGame"
                    wire:loading.attr="disabled"
                    wire:target="startNextGame"
                    size="lg"
                    variant="primary"
                    class="min-h-[44px] px-5 py-3 text-base w-full sm:w-auto hover-scale"
                >
                    <div wire:loading.remove wire:target="startNextGame" class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Quick Start Next Game
                    </div>
                    <div wire:loading wire:target="startNextGame" class="flex items-center">
                        <svg class="mr-2 h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Starting...
                    </div>
                </flux:button>
            </div>
        </div>
    @endif
</div>
