<section class="w-full">
    <form wire:submit="create" class="my-6 w-full space-y-6">
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
        />

        <flux:input
            wire:model="nickname"
            :label="__('Nickname')"
            type="text"
            autocomplete="nickname"
            :description="__('Optional identifier that can be used instead of the full name')"
        />

        <flux:input
            wire:model="email"
            :label="__('Email')"
            type="email"
            required
            autocomplete="email"
        />

        <div>
            <flux:select
                wire:model="role"
                :label="__('Role')"
                required
            >
                @foreach($this->roles as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Determines what permissions the user will have in the system') }}
            </flux:text>
        </div>

        <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <flux:icon icon="information-circle" class="h-5 w-5 text-blue-400 dark:text-blue-300"/>
                </div>
                <div class="ml-3 flex-1">
                    <flux:text class="text-sm !text-blue-700 !dark:text-blue-300">
                        {{ __('A secure random password will be generated for this user. They can use passwordless login or reset their password.') }}
                    </flux:text>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
                <flux:button
                    href="{{ route('admin.users.index') }}"
                    wire:navigate
                    variant="outline"
                >
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="create">{{ __('Create User') }}</span>
                    <span wire:loading wire:target="create" class="flex items-center">
                            <flux:icon icon="arrow-path" class="mr-2 h-4 w-4 animate-spin"/>
                            {{ __('Creating...') }}
                        </span>
                </flux:button>
            </div>

            @if(session('success'))
                <flux:text class="font-medium !text-green-600 !dark:text-green-400">
                    {{ session('success') }}
                </flux:text>
            @endif
        </div>
    </form>
</section>
