<div>
    <form wire:submit="create" class="space-y-6">
        <div class="space-y-5">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Name
                </label>
                <div class="mt-1">
                    <input
                        wire:model="name"
                        type="text"
                        id="name"
                        autocomplete="name"
                        class="w-full rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm shadow-sm transition-colors focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                </div>
                @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nickname -->
            <div>
                <label for="nickname" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Nickname (optional)
                </label>
                <div class="mt-1">
                    <input
                        wire:model="nickname"
                        type="text"
                        id="nickname"
                        autocomplete="nickname"
                        class="w-full rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm shadow-sm transition-colors focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                </div>
                @error('nickname')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Email address
                </label>
                <div class="mt-1">
                    <input
                        wire:model="email"
                        type="email"
                        id="email"
                        autocomplete="email"
                        class="w-full rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm shadow-sm transition-colors focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                </div>
                @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Role
                </label>
                <div class="mt-1">
                    <select
                        wire:model="role"
                        id="role"
                        class="w-full rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm shadow-sm transition-colors focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        @foreach($this->roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @error('role')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Note -->
            <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            A secure random password will be generated for this user. They can use passwordless login or
                            reset their password.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a
                href="{{ route('users.index') }}"
                wire:navigate
                class="inline-flex items-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
            >
                Cancel
            </a>
            <button
                type="submit"
                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                wire:loading.attr="disabled"
                x-bind:disabled="$wire.submitting"
            >
                <span wire:loading.remove wire:target="create">Create User</span>
                <span wire:loading wire:target="create">
                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                </span>
            </button>
        </div>
    </form>
</div>
