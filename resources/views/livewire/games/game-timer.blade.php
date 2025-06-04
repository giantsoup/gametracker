<div class="space-y-4">
    <div>
        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Game Timer</h3>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Track the duration of this game.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Game Status</h4>
            <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-800 dark:text-{{ $statusColor }}-100">
                    {{ $statusLabel }}
                </span>
            </p>
        </div>

        <div>
            <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Timer Status</h4>
            <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                @if($isRunning)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                        Running
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                        Stopped
                    </span>
                @endif
            </p>
        </div>

        <div>
            <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Duration</h4>
            <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                {{ $totalDurationForHumans }}
                @if($isRunning)
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">(Current session: {{ floor($currentSessionDuration / 60) }}h {{ $currentSessionDuration % 60 }}m)</span>
                @endif
            </p>
        </div>

        <div>
            <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Accumulated Duration</h4>
            <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                {{ floor($accumulatedDuration / 60) }}h {{ $accumulatedDuration % 60 }}m
            </p>
        </div>
    </div>

    <div class="flex flex-col gap-4 pt-4">
        <div class="flex items-center gap-4">
            @if($isRunning)
                <flux:button
                    wire:click="stopGame"
                    variant="danger"
                >
                    {{ __('Stop Game') }}
                </flux:button>
            @else
                <flux:button
                    wire:click="startGame"
                    variant="success"
                >
                    {{ __('Start Game') }}
                </flux:button>
            @endif
        </div>

        <div>
            <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-2">Change Game Status</h4>
            <div class="flex items-center gap-2">
                <flux:button
                    wire:click="setStatusUnplayed"
                    variant="outline"
                    size="sm"
                    :disabled="$status->value === 'unplayed'"
                >
                    {{ __('Unplayed') }}
                </flux:button>

                <flux:button
                    wire:click="setStatusActive"
                    variant="outline"
                    size="sm"
                    :disabled="$status->value === 'active'"
                >
                    {{ __('Active') }}
                </flux:button>

                <flux:button
                    wire:click="setStatusPlayed"
                    variant="outline"
                    size="sm"
                    :disabled="$status->value === 'played'"
                >
                    {{ __('Played') }}
                </flux:button>
            </div>
        </div>
    </div>
</div>
