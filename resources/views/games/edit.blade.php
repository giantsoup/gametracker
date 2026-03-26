<x-layouts.app :title="__('Edit Game')">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <flux:button href="{{ route('games.show', $game) }}" variant="subtle" size="sm" icon="arrow-left" wire:navigate />
                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $game->name }}</span>
            </div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Edit Game</h1>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6">
            <livewire:games.edit-game-form :game="$game"/>
        </div>
    </div>
</x-layouts.app>
