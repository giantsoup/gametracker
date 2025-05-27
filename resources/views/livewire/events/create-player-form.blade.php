<div>
    <div class="mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Add Player</h3>
            <flux:button
                wire:click="toggleForm"
                variant="primary"
            >
                {{ $showForm ? 'Cancel' : 'Add Player' }}
            </flux:button>
        </div>

        @if ($showForm)
            <div class="mt-4 bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
                <div class="">
                    <div class="space-y-4">
                        <div>
                            <flux:select
                                wire:model="userId"
                                id="userId"
                                label="Select User"
                                required
                            >
                                <flux:select.option value="">Select a user</flux:select.option>
                                @foreach ($users as $user)
                                    <flux:select.option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            @error('userId')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="nickname"
                                id="nickname"
                                label="Nickname (Optional)"
                                type="text"
                            />
                            @error('nickname')
                            <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <flux:button
                                wire:click="createPlayer"
                                variant="primary"
                            >
                                Add Player
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
