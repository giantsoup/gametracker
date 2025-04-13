<?php

use App\Auth\PasswordlessUserGuard;
use App\Models\User;
use App\Notifications\PasswordlessLoginNotification;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    // For passwordless login toggle
    public bool $usePasswordless = true;

    // Status message for passwordless login
    public string $passwordlessStatus = '';

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        // If using passwordless login, send login link
        if ($this->usePasswordless) {
            $this->sendPasswordlessLoginLink();
            return;
        }

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Send a passwordless login link to the user's email.
     */
    public function sendPasswordlessLoginLink(): void
    {
        // Validate email exists in users table
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        /** @var PasswordlessUserGuard $guard */
        $guard = Auth::guard('passwordless');
        $loginLink = $guard->attemptPasswordlessLogin($this->email);

        if (!$loginLink) {
            throw ValidationException::withMessages([
                'email' => __('This email is not registered or cannot use passwordless login.'),
            ]);
        }

        // Send the login link via notification
        $user = User::where('email', $this->email)->first();
        $user->notify(new PasswordlessLoginNotification($loginLink));

        $this->passwordlessStatus = __('We have sent a login link to your email address!');
    }

    /**
     * Toggle between password and passwordless login methods.
     */
    public function toggleLoginMethod(): void
    {
        $this->usePasswordless = !$this->usePasswordless;
        $this->passwordlessStatus = '';
        $this->resetValidation();
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')"
                   :description="__('Enter your email and password below to log in')"/>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')"/>

    <!-- Passwordless Login Status -->
    @if ($passwordlessStatus)
        <div class="text-center text-sm font-medium text-green-600 dark:text-green-400">
            {{ $passwordlessStatus }}
        </div>
    @endif

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        @if (!$usePasswordless)
            <div class="relative">
                <flux:input
                    wire:model="password"
                    :label="__('Password')"
                    type="password"
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    required="required"
                />

                @if (Route::has('password.request'))
                    <flux:link wire:navigate class="absolute end-0 top-0 text-sm" :href="route('password.request')">
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Remember me')"/>
        @endif

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">
                {{ $usePasswordless ? __('Send Login Link') : __('Log in') }}
            </flux:button>
        </div>
    </form>

    <!-- Toggle login method -->
    <div class="text-center">
        <flux:button variant="ghost" wire:click="toggleLoginMethod" type="button">
            {{ $usePasswordless
                ? __('Use password to login')
                : __('Login with email link (passwordless)')
            }}
        </flux:button>
    </div>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Don\'t have an account?') }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</div>
