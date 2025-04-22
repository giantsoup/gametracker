<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use function Livewire\Volt\{state, rules, mount, computed, protect};

// Define state properties
state([
    'name' => '',
    'nickname' => '',
    'email' => '',
    'password' => '',
    'role' => 'user',
    'roles' => [], // Move roles to the state instead of public property
])->locked('roles'); // Lock roles since they should not be modified by the client

// Set validation rules using best practices
rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    'password' => ['nullable', Password::defaults()],
    'role' => ['required', 'string'],
])->messages([
    'email.unique' => 'This email address is already in use.',
])->attributes([
    'role' => 'user role',
]);

// Mount the component and initialize roles
mount(function ($roles = [], $parentId = null) {
    // If roles were passed to the component, use them
    if (!empty($roles)) {
        $this->roles = $roles;
    }
    if (!empty($parentId)) {
        $this->parentId = $parentId;
    }
});

// Computed property for showing a password info message
$showPasswordInfo = computed(function () {
    return empty($this->password);
});

// Create user action with proper error handling
$create = function () {
    $this->validate();

    try {
        // Create user with secure password (generate if empty)
        User::create([
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'password' => $this->password
                ? Hash::make($this->password)
                : Hash::make(bin2hex(random_bytes(16))),
            'role' => $this->role,
        ]);

        // Reset form fields
        $this->reset(['name', 'nickname', 'email', 'password', 'role']);

        // Show a success message
        session()->flash('success', 'User created successfully');

        // Dispatch event for parent components
        $this->dispatch('user-created')->to($this->parentId);
    } catch (Exception $e) {
        session()->flash('error', 'Failed to create user: '.$e->getMessage());
    }
};

// Then the protected helper can be correctly defined
// Protected helper for generating secure passwords
$generateSecurePassword = protect(function () {
    return bin2hex(random_bytes(16));
});

?>

<section class="w-full">
    <form wire:submit="create" class="w-full space-y-6">
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

        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            autocomplete="new-password"
            :description="__('Leave blank to generate a secure random password')"
        />

        <div>
            <flux:select
                wire:model="role"
                :label="__('Role')"
                required
            >
                @foreach($roles as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Determines what permissions the user will have in the system') }}
            </flux:text>
        </div>

        @if($this->showPasswordInfo)
            <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <flux:icon icon="information-circle" class="h-5 w-5 text-blue-400 dark:text-blue-300"/>
                    </div>
                    <div class="ml-3 flex-1">
                        <flux:text class="text-sm text-blue-400 dark:text-blue-300">
                            {{ __('A secure random password will be generated for this user. They can use passwordless login or reset their password.') }}
                        </flux:text>
                    </div>
                </div>
            </div>
        @endif

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

            @if(session('error'))
                <flux:text class="font-medium !text-red-600 !dark:text-red-400">
                    {{ session('error') }}
                </flux:text>
            @endif
        </div>
    </form>
</section>
