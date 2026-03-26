<x-layouts.app :title="__('Events')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Events</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">View and manage your game night events.</p>
        </div>

        @if ($events->isEmpty())
            <div class="rounded-xl border border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800/50 p-10 text-center">
                <flux:icon.calendar class="mx-auto h-10 w-10 text-zinc-400 dark:text-zinc-500" />
                <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-zinc-100">No events found</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">There are no events available at this time.</p>
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="py-3 pl-5 pr-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Name
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Status
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400 hidden sm:table-cell">
                                Start Date
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400 hidden md:table-cell">
                                End Date
                            </th>
                            <th scope="col" class="relative py-3 pl-3 pr-5">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800/50">
                        @foreach ($events as $event)
                            <tr>
                                <td class="whitespace-nowrap py-3.5 pl-5 pr-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $event->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm">
                                    @if($event->active)
                                        <flux:badge color="green" variant="pill" size="sm">Active</flux:badge>
                                    @else
                                        <flux:badge color="zinc" variant="pill" size="sm">Inactive</flux:badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm text-zinc-500 dark:text-zinc-400 hidden sm:table-cell">
                                    {{ $event->starts_at ? $event->starts_at->format('M j, Y g:i A') : 'Not set' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-3.5 text-sm text-zinc-500 dark:text-zinc-400 hidden md:table-cell">
                                    {{ $event->ends_at ? $event->ends_at->format('M j, Y g:i A') : 'Not set' }}
                                </td>
                                <td class="whitespace-nowrap py-3.5 pl-3 pr-5 text-right text-sm">
                                    <flux:button
                                        href="{{ route('events.show', $event) }}"
                                        variant="subtle"
                                        size="sm"
                                        wire:navigate
                                    >
                                        {{ __('View') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
