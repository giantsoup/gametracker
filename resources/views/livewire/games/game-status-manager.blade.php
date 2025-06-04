<div>
    <!-- Status Badge -->
    <div class="mb-2">
        @if($this->status)
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                @if($this->status === \App\Enums\GameStatus::Ready)
                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                @elseif($this->status === \App\Enums\GameStatus::Playing)
                    bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                @elseif($this->status === \App\Enums\GameStatus::Finished)
                    bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                @elseif($this->status === \App\Enums\GameStatus::Background)
                    bg-neutral-100 text-neutral-800 dark:bg-neutral-900/20 dark:text-neutral-300
                @endif
            ">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2
                    @if($this->status === \App\Enums\GameStatus::Ready)
                        text-yellow-400
                    @elseif($this->status === \App\Enums\GameStatus::Playing)
                        text-blue-400
                    @elseif($this->status === \App\Enums\GameStatus::Finished)
                        text-green-400
                    @elseif($this->status === \App\Enums\GameStatus::Background)
                        text-neutral-400
                    @endif
                " fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
                {{ $this->status->label() }}
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-900/20 dark:text-neutral-300">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-neutral-400" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
                New Game
            </span>
        @endif
    </div>

    <!-- Status Transition Buttons -->
    <div class="flex flex-wrap gap-3">
        @if($this->status === null || $this->status === \App\Enums\GameStatus::Background)
            <flux:button
                wire:click="confirmAction('markAsReady')"
                wire:loading.attr="disabled"
                wire:target="markAsReady"
                size="lg"
                variant="outline"
                class="border-yellow-300 bg-yellow-50 text-yellow-700 hover:bg-yellow-100 dark:border-yellow-800 dark:bg-yellow-950/30 dark:text-yellow-300 dark:hover:bg-yellow-900/30 min-h-[44px] px-5 py-3 text-base"
            >
                <div wire:loading.remove wire:target="markAsReady">
                    Mark as Ready
                </div>
                <div wire:loading wire:target="markAsReady" class="flex items-center">
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </div>
            </flux:button>
        @endif

        @if($this->status === \App\Enums\GameStatus::Ready)
            <flux:button
                wire:click="confirmAction('markAsPlaying')"
                wire:loading.attr="disabled"
                wire:target="markAsPlaying"
                size="lg"
                variant="outline"
                class="border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/30 dark:text-blue-300 dark:hover:bg-blue-900/30 min-h-[44px] px-5 py-3 text-base"
            >
                <div wire:loading.remove wire:target="markAsPlaying">
                    Start Playing
                </div>
                <div wire:loading wire:target="markAsPlaying" class="flex items-center">
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </div>
            </flux:button>
        @endif

        @if($this->status === \App\Enums\GameStatus::Playing)
            <flux:button
                wire:click="confirmAction('markAsFinished')"
                wire:loading.attr="disabled"
                wire:target="markAsFinished"
                size="lg"
                variant="outline"
                class="border-green-300 bg-green-50 text-green-700 hover:bg-green-100 dark:border-green-800 dark:bg-green-950/30 dark:text-green-300 dark:hover:bg-green-900/30 min-h-[44px] px-5 py-3 text-base"
            >
                <div wire:loading.remove wire:target="markAsFinished">
                    Mark as Finished
                </div>
                <div wire:loading wire:target="markAsFinished" class="flex items-center">
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </div>
            </flux:button>
        @endif

        @if($this->status !== \App\Enums\GameStatus::Background)
            <flux:button
                wire:click="confirmAction('markAsBackground')"
                wire:loading.attr="disabled"
                wire:target="markAsBackground"
                size="lg"
                variant="outline"
                class="border-neutral-300 bg-neutral-50 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/30 dark:text-neutral-300 dark:hover:bg-neutral-900/30 min-h-[44px] px-5 py-3 text-base"
            >
                <div wire:loading.remove wire:target="markAsBackground">
                    Mark as Background
                </div>
                <div wire:loading wire:target="markAsBackground" class="flex items-center">
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </div>
            </flux:button>
        @endif
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-800">
                <h3 class="text-lg font-medium text-neutral-900 dark:text-white">
                    Confirm Status Change
                </h3>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                    @if($confirmAction === 'markAsReady')
                        Are you sure you want to mark this game as ready to start?
                    @elseif($confirmAction === 'markAsPlaying')
                        Are you sure you want to start playing this game?
                    @elseif($confirmAction === 'markAsFinished')
                        Are you sure you want to mark this game as finished?
                    @elseif($confirmAction === 'markAsBackground')
                        Are you sure you want to mark this game as a background game?
                    @endif
                </p>
                <div class="mt-4 flex justify-end space-x-4">
                    <flux:button
                        wire:click="cancelConfirmation"
                        variant="outline"
                        size="lg"
                        class="min-h-[44px] px-5 py-3 text-base"
                    >
                        Cancel
                    </flux:button>
                    <flux:button
                        wire:click="executeConfirmedAction"
                        wire:loading.attr="disabled"
                        wire:target="executeConfirmedAction"
                        variant="primary"
                        size="lg"
                        class="min-h-[44px] px-5 py-3 text-base"
                    >
                        <div wire:loading.remove wire:target="executeConfirmedAction">
                            Confirm
                        </div>
                        <div wire:loading wire:target="executeConfirmedAction" class="flex items-center">
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </div>
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
