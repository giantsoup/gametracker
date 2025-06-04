<div class="w-full">
    <!-- Error message if game is not in Finished status -->
    @if(session()->has('error'))
        <div class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</h3>
                </div>
            </div>
        </div>
    @else
        <div class="space-y-6">
            <!-- Progress indicator -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Step {{ $currentStep }} of {{ $totalSteps }}
                    </span>
                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        {{ floor(($currentStep / $totalSteps) * 100) }}% Complete
                    </span>
                </div>
                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                    <div class="h-2 rounded-full bg-blue-600 transition-all duration-300 ease-in-out dark:bg-blue-500"
                         style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
                </div>
            </div>

            <!-- Step content -->
            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                @if($currentStep < $totalSteps)
                    <!-- Player selection steps -->
                    <div class="text-center">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
                            Select {{ $this->getOrdinal($currentStep) }} Place
                        </h2>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                            Tap on a player to assign them to {{ $this->getOrdinal($currentStep) }} place.
                        </p>
                    </div>

                    <!-- Player selection grid -->
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach($remainingPlayers as $player)
                            <button
                                wire:click="selectPlayer({{ $player->id }})"
                                class="flex items-center justify-between rounded-lg border-2 border-neutral-200 bg-white p-4 text-left shadow-sm transition-all hover:border-blue-500 hover:bg-blue-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-500 dark:hover:bg-blue-900/20"
                            >
                                <div class="flex items-center">
                                    <!-- Player avatar/initials -->
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-200 text-lg font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                        {{ substr($player->getDisplayName(), 0, 2) }}
                                    </div>

                                    <!-- Player name -->
                                    <div class="ml-4">
                                        <div class="text-lg font-medium text-neutral-900 dark:text-white">
                                            {{ $player->getDisplayName() }}
                                        </div>
                                        @if($player->user)
                                            <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                                {{ $player->user->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Points preview -->
                                <div class="ml-4 text-right">
                                    <div class="text-lg font-medium text-blue-600 dark:text-blue-400">
                                        {{ $game->getPointsForPlacement($currentStep) }} pts
                                    </div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $this->getOrdinal($currentStep) }} place
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <!-- Confirmation step -->
                    <div class="text-center">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
                            Confirm Placements
                        </h2>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                            Review the placements and points before saving.
                        </p>
                    </div>

                    <!-- Placements summary -->
                    <div class="mt-6 overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-50 dark:bg-neutral-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                        Placement
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                        Player
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                        Points
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">
                                @foreach($selectedPlayers as $placement => $player)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                            {{ $this->getOrdinal($placement) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ $player->getDisplayName() }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-blue-600 dark:text-blue-400">
                                            {{ $playerPoints[$player->user_id] ?? 0 }} pts
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- Total row -->
                                <tr class="bg-neutral-50 dark:bg-neutral-800">
                                    <td colspan="2" class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-neutral-900 dark:text-white">
                                        Total Points:
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-blue-600 dark:text-blue-400">
                                        {{ $totalPoints }} pts
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Save button -->
                    <div class="mt-6 flex justify-center">
                        <flux:button
                            wire:click="showConfirmation"
                            variant="primary"
                            size="lg"
                            class="w-full sm:w-auto"
                        >
                            Save Placements & Points
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Navigation buttons -->
            <div class="flex justify-between">
                @if($currentStep > 1)
                    <flux:button
                        wire:click="previousStep"
                        variant="outline"
                        size="lg"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </flux:button>
                @else
                    <div></div>
                @endif

                @if($currentStep < $totalSteps - 1)
                    <flux:button
                        wire:click="nextStep"
                        variant="outline"
                        size="lg"
                    >
                        Skip
                        <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Confirmation Modal -->
        @if($showConfirmation)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-800">
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-white">
                        Confirm Points Assignment
                    </h3>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Are you sure you want to save these placements and points? This action cannot be undone.
                    </p>
                    <div class="mt-4 flex justify-end space-x-3">
                        <flux:button wire:click="cancelConfirmation" variant="outline" size="sm">
                            Cancel
                        </flux:button>
                        <flux:button
                            wire:click="savePoints"
                            variant="primary"
                            size="sm"
                        >
                            Confirm
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('points-saved', () => {
            // Show success notification
            if (window.showNotification) {
                window.showNotification('Points saved successfully!', 'success');
            }
        });
    });
</script>
@endpush
