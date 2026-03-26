<x-layouts.app :title="__('Edit User')">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <flux:button href="{{ route('admin.users.show', $user) }}" variant="subtle" size="sm" icon="arrow-left" />
                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->name }}</span>
            </div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Edit User</h1>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 sm:p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

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
                    <flux:text class="text-sm !text-red-600 !dark:text-red-400">{{ $message }}</flux:text>
                @enderror

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
                    <flux:text class="text-sm !text-red-600 !dark:text-red-400">{{ $message }}</flux:text>
                @enderror

                <div>
                    <flux:select name="role" id="role" :label="__('Role')" required>
                        @foreach ($roles as $value => $label)
                            <flux:select.option
                                value="{{ $value }}"
                                :selected="old('role', $user->role->value) == $value"
                            >
                                {{ $label }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Determines what permissions the user will have in the system.') }}
                    </flux:text>
                    @error('role')
                        <flux:text class="text-sm !text-red-600 !dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <flux:button href="{{ route('admin.users.index') }}" variant="outline">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Update User') }}
                    </flux:button>

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
</x-layouts.app>
