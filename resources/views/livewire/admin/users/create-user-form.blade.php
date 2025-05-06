<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Livewire\Volt\{state, rules, mount, computed, protect};

// Define state properties with locked roles
state([
    'name' => '',
    'nickname' => '',
    'email' => '',
    'password' => '',
    'role' => 'user',
    'roles' => [],
    'parentId' => null,
    'debug' => [], // Add a debug state property to store debug information
    'validationRules' => [] // Add state for dynamic validation rules
]);

// Mount component with initial data and set up initial validation rules
mount(function ($roles = [], $parentId = null) {
    if (!empty($roles)) {
        $this->roles = $roles;
    }
    if (!empty($parentId)) {
        $this->parentId = $parentId;
    }

    // Set initial validation rules based on role
    $this->updateValidationRules();
});

// Method to update validation rules based on role
$updateValidationRules = function() {
    $baseRules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'role' => ['required', 'string'],
    ];

    // Set password rules based on role
    if ($this->role === 'admin') {
        $baseRules['password'] = ['required', 'string', 'min:8'];
    } else {
        $baseRules['password'] = ['nullable', 'string', 'min:8'];
    }

    $this->validationRules = $baseRules;
};

// Set base validation rules
rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    'password' => ['nullable', 'string', 'min:8'],
    'role' => ['required', 'string'],
])->messages([
    'email.unique' => 'This email address is already in use.',
    'password.required' => 'Password is required for admin users.',
])->attributes([
    'role' => 'user role',
]);

// Computed property for showing password field
$showPasswordField = computed(function () {
    return $this->role === 'admin';
});

// Debug function to log role changes
$roleChanged = function() {
    // Update validation rules when role changes
    $this->updateValidationRules();

    // Update debug information
    $this->debug = [
        'role' => $this->role,
        'showPasswordField' => $this->showPasswordField,
        'timestamp' => now()->format('H:i:s.u')
    ];

    // Optionally, you can also log to browser console
    $this->dispatch('console-log', ['role' => $this->role, 'showPasswordField' => $this->showPasswordField]);
};

// Create user action with manual validation
$create = function () {
    // Validate using the dynamic rules
    $this->validate($this->validationRules);

    try {
        User::create([
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'password' => $this->password
                ? Hash::make($this->password)
                : Hash::make($this->generateSecurePassword()),
            'role' => $this->role,
        ]);

        // Reset form fields
        $this->reset(['name', 'nickname', 'email', 'password', 'role']);

        // Show a success message
        session()->flash('success', 'User created successfully');

        // Dispatch event for parent components
        if ($this->parentId) {
            $this->dispatch('user-created')->to($this->parentId);
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to create user: '.$e->getMessage());
    }
};

// Protected helper for generating secure passwords
$generateSecurePassword = protect(function () {
    return bin2hex(random_bytes(16));
});

?>

<section class="w-full">
    <form wire:submit="create" class="w-full space-y-6">
        <!-- Debug information display -->
        @if(!empty($debug))
            <div class="p-4 mb-4 bg-gray-100 dark:bg-gray-800 rounded-md overflow-auto">
                <h3 class="text-sm font-bold mb-2">Debug Info ({{ $debug['timestamp'] ?? 'N/A' }}):</h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>Role: <span class="font-mono">{{ $debug['role'] ?? 'N/A' }}</span></div>
                    <div>ShowPasswordField: <span class="font-mono">{{ $debug['showPasswordField'] ? 'true' : 'false' }}</span></div>
                </div>
            </div>
        @endif

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

        @if($this->showPasswordField)
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            autocomplete="new-password"
            required
        />
        @endif

        <div>
            <flux:select
                wire:model.live="role"
                {{-- wire:change="roleChanged" --}}
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

        @if(!$this->showPasswordField)
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

<!-- Optional: JavaScript to handle console logging -->
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('console-log', (data) => {
            console.log('Debug info:', data);
        });
    });
</script>
