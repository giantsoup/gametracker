<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Enums\UserRole;
use function Livewire\Volt\{state, rules, mount, computed, protect};

// Define state properties
state([
    'user' => null,
    'name' => '',
    'nickname' => '',
    'email' => '',
    'password' => '',
    'role' => 'user',
    'roles' => [], // Move roles to the state instead of public property
])->locked(['roles', 'user']); // Lock roles and user since they should not be modified by the client

// Set validation rules using best practices
rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255'],
    'password' => ['nullable', Password::defaults()],
    'role' => ['required', 'string'],
])->messages([
    'email.unique' => 'This email address is already in use.',
])->attributes([
    'role' => 'user role',
]);

// Mount the component and initialize roles and user data
mount(function ($user, $roles = [], $parentId = null) {
    // Set the user
    $this->user = $user;

    // Initialize form fields with user data
    $this->name = $user->name;
    $this->nickname = $user->nickname;
    $this->email = $user->email;
    $this->role = $user->role->value;

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
    return !empty($this->password);
});

// Computed property to check if selected role is admin
$isAdminRole = computed(function () {
    return $this->role === UserRole::ADMIN->value;
});

// Update user action with proper error handling
$update = function () {
    // Add unique rule for email, excluding the current user
    $this->rules['email'][] = 'unique:users,email,' . $this->user->id;

    $this->validate();

    try {
        // Update user data
        $userData = [
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'role' => $this->role,
        ];

        // Only update password if admin role is selected and password provided
        if ($this->isAdminRole && !empty($this->password)) {
            $userData['password'] = Hash::make($this->password);
        }

        $this->user->update($userData);

        // Show a success message
        session()->flash('success', 'User updated successfully');

        // Dispatch event for parent components
        $this->dispatch('user-updated')->to($this->parentId);
    } catch (Exception $e) {
        session()->flash('error', 'Failed to update user: '.$e->getMessage());
    }
};

// Protected helper for generating secure passwords
$generateSecurePassword = protect(function () {
    return bin2hex(random_bytes(16));
});

?>

<section class="w-full">
    <form wire:submit="update" class="w-full space-y-6">
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

        @if($this->isAdminRole)
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            autocomplete="new-password"
            :description="__('Leave blank to keep the current password')"
        />
        @endif

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

        @if($this->isAdminRole && $this->showPasswordInfo)
            <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <flux:icon icon="information-circle" class="h-5 w-5 text-blue-400 dark:text-blue-300"/>
                    </div>
                    <div class="ml-3 flex-1">
                        <flux:text class="text-sm text-blue-400 dark:text-blue-300">
                            {{ __('The user\'s password will be updated to the new value you provided.') }}
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
                    <span wire:loading.remove wire:target="update">{{ __('Update User') }}</span>
                    <span wire:loading wire:target="update" class="flex items-center">
                        <flux:icon icon="arrow-path" class="mr-2 h-4 w-4 animate-spin"/>
                        {{ __('Updating...') }}
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
