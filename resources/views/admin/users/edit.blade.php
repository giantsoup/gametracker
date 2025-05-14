<x-layouts.app>
    <x-slot:header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-zinc-200">
            Edit User: {{ $user->name }}
        </h2>
    </x-slot:header>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white dark:bg-zinc-800 shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <flux:input
                                name="name"
                                id="name"
                                :label="__('Name')"
                                type="text"
                                value="{{ old('name', $user->name) }}"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            @error('name')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                name="email"
                                id="email"
                                :label="__('Email')"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                autocomplete="email"
                            />
                            @error('email')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:select
                                name="role"
                                id="role"
                                :label="__('Role')"
                                required
                            >
                                @foreach ($roles as $value => $label)
                                    <flux:select.option
                                        value="{{ $value }}"
                                        :selected="old('role', $user->role->value) == $value"
                                    >
                                        {{ $label }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Determines what permissions the user will have in the system') }}
                            </flux:text>
                            @error('role')
                                <flux:text class="mt-2 text-sm !text-red-600 !dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <div class="flex items-center gap-3">
                                <flux:button
                                    href="{{ route('admin.users.index') }}"
                                    variant="outline"
                                >
                                    {{ __('Cancel') }}
                                </flux:button>

                                <flux:button
                                    type="submit"
                                    variant="primary"
                                >
                                    {{ __('Update User') }}
                                </flux:button>
                            </div>

                            @if(session('success'))
                                <flux:text class="font-medium !text-green-600 !dark:text-green-400">
                                    {{ session('success') }}
                                </flux:text>
                            @endif

                            @if(session('error'))
                                <flux:text class="font-medium !text-red-600 !dark:text-red-400">
                                    {{ session('error') }}
                                </flux:text>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app><?php
