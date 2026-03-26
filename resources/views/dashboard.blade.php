<x-layouts.app :title="__('Dashboard')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Welcome back, {{ auth()->user()->name }}.</p>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Active Events</p>
                <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ \App\Models\Event::active()->count() }}
                </p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Games</p>
                <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ \App\Models\Game::count() }}
                </p>
            </div>
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Total Events</p>
                <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ \App\Models\Event::count() }}
                </p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('events.index') }}" wire:navigate
               class="group rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 transition hover:border-zinc-300 dark:hover:border-zinc-600">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-700">
                        <flux:icon.calendar class="h-5 w-5 text-zinc-600 dark:text-zinc-300" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Events</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">View and manage your game night events</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('games.index') }}" wire:navigate
               class="group rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 transition hover:border-zinc-300 dark:hover:border-zinc-600">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-700">
                        <flux:icon.puzzle-piece class="h-5 w-5 text-zinc-600 dark:text-zinc-300" />
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Games</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Browse all board games played</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-layouts.app>
