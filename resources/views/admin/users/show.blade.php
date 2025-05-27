<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-zinc-200">
                User Details
            </h2>
            <div class="flex space-x-2">
                <flux:button
                    href="{{ route('admin.users.edit', $user) }}"
                    variant="primary"
                >
                    {{ __('Edit User') }}
                </flux:button>
                <flux:button
                    href="{{ route('admin.users.index') }}"
                    variant="outline"
                >
                    {{ __('Back to Users') }}
                </flux:button>
            </div>
        </div>
    </x-slot:header>

    <div class="">
        <div class="mx-auto max-w-7xl">
            <div
                class="overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                    @if (session('success'))
                        <div
                            class="p-4 mb-4 text-green-700 bg-green-100 dark:bg-green-900/20 dark:text-green-300 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-200">User Information</h3>
                                <div class="mt-2 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <span
                                                class="text-sm font-medium text-gray-500 dark:text-zinc-400">Name</span>
                                            <p class="mt-1 text-gray-900 dark:text-zinc-300">{{ $user->name }}</p>
                                        </div>
                                        <div>
                                            <span
                                                class="text-sm font-medium text-gray-500 dark:text-zinc-400">Email</span>
                                            <p class="mt-1 text-gray-900 dark:text-zinc-300">{{ $user->email }}</p>
                                        </div>
                                        <div>
                                            <span
                                                class="text-sm font-medium text-gray-500 dark:text-zinc-400">Role</span>
                                            <p class="mt-1">
                                                @php $badge = $user->getRoleBadge(); @endphp
                                                <flux:badge variant="pill"
                                                            color="{{ $badge['color'] }}">{{ $badge['text'] }}</flux:badge>
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-zinc-400">Account Created</span>
                                            <p class="mt-1 text-gray-900 dark:text-zinc-300">{{ $user->created_at->format('F j, Y') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-zinc-400">Last Updated</span>
                                            <p class="mt-1 text-gray-900 dark:text-zinc-300">{{ $user->updated_at->format('F j, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-200">Actions</h3>
                                <div class="mt-2 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg space-y-4">
                                    <flux:button
                                        href="{{ route('admin.users.edit', $user) }}"
                                        variant="primary"
                                        class="w-full justify-center"
                                    >
                                        {{ __('Edit User') }}
                                    </flux:button>

                                    @if (auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button
                                                type="submit"
                                                variant="danger"
                                                class="w-full justify-center"
                                            >
                                                {{ __('Delete User') }}
                                            </flux:button>
                                        </form>
                                    @else
                                        <div
                                            class="p-4 text-sm text-blue-700 bg-blue-100 dark:bg-blue-900/20 dark:text-blue-300 rounded-md text-center">
                                            {{ __('You cannot delete your own account') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
