<x-layouts.app :title="$user->name">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <flux:button href="{{ route('admin.users.index') }}" variant="subtle" size="sm" icon="arrow-left" wire:navigate />
                <span class="text-sm text-zinc-500 dark:text-zinc-400">Users</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</h1>
                <flux:button
                    href="{{ route('admin.users.edit', $user) }}"
                    variant="primary"
                    size="sm"
                >
                    {{ __('Edit User') }}
                </flux:button>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 text-sm text-emerald-700 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- User Information --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6 mb-6">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4">User Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Name</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Email</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Role</p>
                    <p class="mt-1">
                        @php $badge = $user->getRoleBadge(); @endphp
                        <flux:badge variant="pill" color="{{ $badge['color'] }}">{{ $badge['text'] }}</flux:badge>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Account Created</p>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $user->created_at->format('F j, Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Actions</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <flux:button
                    href="{{ route('admin.users.edit', $user) }}"
                    variant="primary"
                    size="sm"
                >
                    {{ __('Edit User') }}
                </flux:button>

                @if (auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <flux:button type="submit" variant="danger" size="sm">
                            {{ __('Delete User') }}
                        </flux:button>
                    </form>
                @else
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">You cannot delete your own account.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
