<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-zinc-200">
                User Details
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600/90 dark:hover:bg-indigo-500 rounded-md">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 dark:text-zinc-200 rounded-md">
                    Back to Users
                </a>
            </div>
        </div>
    </x-slot:header>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="block w-full px-4 py-2 text-center text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600/90 dark:hover:bg-indigo-500 rounded-md">
                                        Edit User
                                    </a>

                                    @if (auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="block w-full px-4 py-2 text-center text-white bg-red-600 hover:bg-red-700 dark:bg-red-600/90 dark:hover:bg-red-500 rounded-md">
                                                Delete User
                                            </button>
                                        </form>
                                    @else
                                        <div
                                            class="p-2 text-sm text-center text-gray-500 bg-gray-100 dark:bg-zinc-800 dark:text-zinc-400 rounded-md">
                                            You cannot delete your own account
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
