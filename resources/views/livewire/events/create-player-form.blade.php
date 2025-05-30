<div>
    <div class="mt-6">
        @if (session()->has('success'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 dark:text-green-500" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="mb-4 rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
                            {{ session('warning') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Manage Players</h3>
            @if(!$showForm)
                <flux:button
                    wire:click="toggleForm"
                    variant="primary"
                >
                    Edit Players
                </flux:button>
            @endif
        </div>

        @if($showForm)
            <div
                class="mt-2 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg p-5 shadow-sm">
                <div class="">
                    <div>
                        <div class="mb-2">
                            <flux:label for="selectedUserIds" value="Select Users"/>
                            <div class="flex items-center justify-between mt-1 mb-3">
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Check/uncheck to add or remove players
                                </div>
                                <div class="flex space-x-2">
                                    <flux:button
                                        wire:click="selectAll"
                                        variant="outline"
                                        size="xs"
                                        class="cursor-pointer"
                                    >
                                        Select All
                                    </flux:button>
                                    <flux:button
                                        wire:click="deselectAll"
                                        variant="outline"
                                        size="xs"
                                        class="cursor-pointer"
                                    >
                                        Deselect All
                                    </flux:button>
                                </div>
                            </div>
                        </div>

                        <div
                            class="max-h-60 overflow-y-auto border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 p-2">
                                @foreach ($users as $user)
                                    @php
                                        $playerStatus = null;
                                        $statusClass = '';
                                        $statusText = '';

                                        if (isset($eventPlayers[$user->id])) {
                                            $player = $eventPlayers[$user->id];
                                            if ($player->trashed()) {
                                                $playerStatus = 'deleted';
                                                $statusClass = 'text-red-600 dark:text-red-400';
                                                $statusText = '(Deleted)';
                                            } elseif ($player->hasLeft()) {
                                                $playerStatus = 'left';
                                                $statusClass = 'text-yellow-600 dark:text-yellow-400';
                                                $statusText = '(Left)';
                                            }
                                        }
                                    @endphp

                                    <div class="flex items-center">
                                        <input
                                            wire:model.live="selectedUserIds"
                                            id="user-{{ $user->id }}"
                                            type="checkbox"
                                            value="{{ $user->id }}"
                                            class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:focus:ring-blue-600 cursor-pointer"
                                        >
                                        <label for="user-{{ $user->id }}"
                                               class="ml-2 text-sm text-zinc-700 dark:text-zinc-300 {{ $statusClass }} cursor-pointer">
                                            {{ $user->name }}
                                            @if($statusText)
                                                <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @error('selectedUserIds')
                        <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                            {{ $message }}
                        </flux:text>
                        @enderror

                        <div class="flex justify-between items-center mt-4">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ count($selectedUserIds) }} user(s) selected
                            </div>
                            <flux:button
                                wire:click="createPlayer"
                                variant="primary"
                            >
                                Save Changes
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
