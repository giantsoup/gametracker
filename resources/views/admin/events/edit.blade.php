<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Edit Event: {{ $event->name }}
        </h2>
    </x-slot:header>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <livewire:admin.events.edit-event-form :event="$event" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
