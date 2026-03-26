<x-layouts.app :title="__('Games')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Games</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">All board games played across your game nights.</p>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6">
            <livewire:games.games-table/>
        </div>
    </div>
</x-layouts.app>
