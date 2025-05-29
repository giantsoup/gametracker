<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Create New Event
        </h2>
    </x-slot:header>

    <div class="">
        <div class="mx-auto max-w-7xl">
            <div
                class="overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Event Information</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Create a new event by filling out
                                the form below.</p>
                        </div>

                        <form action="{{ route('events.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <div>
                                <flux:input
                                    name="name"
                                    id="name"
                                    :label="__('Event Name')"
                                    type="text"
                                    required
                                    autofocus
                                    :value="old('name')"
                                />
                                @error('name')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                                @enderror
                            </div>

                            <div>
                                <flux:input
                                    name="starts_at"
                                    id="starts_at"
                                    :label="__('Start Date')"
                                    type="datetime-local"
                                    :value="old('starts_at')"
                                />
                                @error('starts_at')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                                @enderror
                            </div>

                            <div>
                                <flux:input
                                    name="ends_at"
                                    id="ends_at"
                                    :label="__('End Date')"
                                    type="datetime-local"
                                    :value="old('ends_at')"
                                />
                                @error('ends_at')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                                @enderror
                            </div>

                            <div>
                                <flux:checkbox
                                    name="active"
                                    id="active"
                                    :label="__('Active')"
                                    :checked="old('active', true)"
                                    value="1"
                                />
                                @error('active')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                                @enderror
                            </div>

                            <div class="flex items-center gap-4">
                                <flux:button
                                    href="{{ route('events.index') }}"
                                    wire:navigate
                                    variant="outline"
                                >
                                    {{ __('Cancel') }}
                                </flux:button>

                                <flux:button
                                    type="submit"
                                    variant="primary"
                                >
                                    {{ __('Create Event') }}
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
