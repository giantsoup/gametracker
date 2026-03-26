<x-layouts.app :title="__('Admin')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">System administration and management.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('admin.users.index') }}"
               class="group rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 transition hover:border-zinc-300 dark:hover:border-zinc-600">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-700">
                        <flux:icon.users class="h-5 w-5 text-zinc-600 dark:text-zinc-300" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Manage Users</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Create, edit and delete user accounts</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-layouts.app>
