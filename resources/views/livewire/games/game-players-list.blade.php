<div>
    <div class="space-y-4">
        <!-- Header with player count and manage button -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <h3 class="text-sm font-medium text-neutral-900 dark:text-white">Players</h3>
                <div class="flex space-x-1">
                    <!-- Active players count -->
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                        <svg class="-ml-0.5 mr-1 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ $activePlayers->count() }} Active
                    </span>

                    <!-- Left players count (if any) -->
                    @if($leftPlayers->count() > 0)
                        <span class="inline-flex items-center rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-900/20 dark:text-neutral-300">
                            <svg class="-ml-0.5 mr-1 h-2 w-2 text-neutral-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            {{ $leftPlayers->count() }} Left
                        </span>
                    @endif
                </div>
            </div>

            <!-- Manage Players Toggle Button -->
            <flux:button
                wire:click="toggleManageMode"
                size="lg"
                variant="{{ $manageMode ? 'primary' : 'outline' }}"
                class="{{ $manageMode ? '' : 'border-neutral-300 bg-neutral-50 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:bg-neutral-950/30 dark:text-neutral-300 dark:hover:bg-neutral-900/30' }} min-h-[44px]"
            >
                {{ $manageMode ? 'Done' : 'Manage Players' }}
            </flux:button>
        </div>

        <!-- Players List -->
        <div class="space-y-2">
            @if($allPlayers->isEmpty())
                <div class="rounded-md bg-neutral-50 p-3 text-center text-sm text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                    No players in this game yet.
                </div>
            @else
                <!-- Active Players -->
                @if($activePlayers->isNotEmpty())
                    <div class="space-y-1">
                        @foreach($activePlayers as $player)
                            <div class="flex items-center justify-between rounded-md border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800">
                                <div class="flex items-center space-x-3">
                                    <!-- Selection Checkbox (only in manage mode) -->
                                    @if($manageMode)
                                        <div class="flex h-8 w-8 items-center justify-center">
                                            <input
                                                type="checkbox"
                                                wire:click="togglePlayerSelection({{ $player->id }})"
                                                class="h-8 w-8 rounded border-neutral-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900"
                                                {{ in_array($player->id, $selectedPlayers) ? 'checked' : '' }}
                                            >
                                        </div>
                                    @endif

                                    <!-- Player Avatar/Initials -->
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-200 text-base font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                        {{ substr($player->getDisplayName(), 0, 2) }}
                                    </div>

                                    <!-- Player Name and Status Indicators -->
                                    <div>
                                        <div class="flex items-center space-x-1">
                                            <span class="text-sm font-medium text-neutral-900 dark:text-white">
                                                {{ $player->getDisplayName() }}
                                            </span>

                                            <!-- Owner Indicator -->
                                            @if($this->isOwner($player))
                                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/20 dark:text-purple-300">
                                                    Owner
                                                </span>
                                            @endif

                                            <!-- Playing Indicator -->
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                Playing
                                            </span>
                                        </div>

                                        <!-- User Email (if available) -->
                                        @if($player->user)
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ $player->user->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Left Players (if any) -->
                @if($leftPlayers->isNotEmpty())
                    <div class="mt-4 space-y-1">
                        <h4 class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Left Players</h4>

                        @foreach($leftPlayers as $player)
                            <div class="flex items-center justify-between rounded-md border border-neutral-200 bg-white p-5 opacity-70 dark:border-neutral-700 dark:bg-neutral-800">
                                <div class="flex items-center space-x-3">
                                    <!-- Player Avatar/Initials -->
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-200 text-base font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                        {{ substr($player->getDisplayName(), 0, 2) }}
                                    </div>

                                    <!-- Player Name and Status Indicators -->
                                    <div>
                                        <div class="flex items-center space-x-1">
                                            <span class="text-sm font-medium text-neutral-500 line-through dark:text-neutral-400">
                                                {{ $player->getDisplayName() }}
                                            </span>

                                            <!-- Owner Indicator -->
                                            @if($this->isOwner($player))
                                                <span class="inline-flex items-center rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-900/20 dark:text-neutral-300">
                                                    Owner
                                                </span>
                                            @endif

                                            <!-- Left Indicator -->
                                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-900/20 dark:text-neutral-300">
                                                Left
                                            </span>
                                        </div>

                                        <!-- User Email (if available) -->
                                        @if($player->user)
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ $player->user->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        <!-- Bulk Actions (only visible in manage mode with selections) -->
        @if($manageMode && !empty($selectedPlayers))
            <div class="mt-4 flex justify-end">
                <flux:button
                    wire:click="confirmMarkAsLeft"
                    variant="outline"
                    size="lg"
                    class="border-red-300 bg-red-50 text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-950/30 dark:text-red-300 dark:hover:bg-red-900/30 min-h-[44px] px-5 py-3 text-base"
                >
                    Mark as Left ({{ count($selectedPlayers) }})
                </flux:button>
            </div>
        @endif

        <!-- Confirmation Modal -->
        @if($showConfirmation)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-800">
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-white">
                        Confirm Action
                    </h3>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Are you sure you want to mark {{ count($selectedPlayers) }} player(s) as having left the game?
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
                            wire:click="markPlayersAsLeft"
                            variant="primary"
                            size="lg"
                            class="bg-red-600 hover:bg-red-700 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-600 min-h-[44px] px-5 py-3 text-base"
                        >
                            Confirm
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
