<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <header class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-app-logo-icon class="size-6 fill-current text-zinc-900 dark:text-white" />
                <span class="text-base font-semibold text-zinc-900 dark:text-zinc-100">GameTracker</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <flux:button href="{{ route('dashboard') }}" variant="primary" size="sm" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:button>
                @else
                    <flux:button href="{{ route('login') }}" variant="subtle" size="sm" wire:navigate>
                        {{ __('Log in') }}
                    </flux:button>
                    <flux:button href="{{ route('register') }}" variant="primary" size="sm" wire:navigate>
                        {{ __('Sign up') }}
                    </flux:button>
                @endauth
            </div>
        </div>
    </header>

    @hasSection('content')
        @yield('content')
    @else
        {{ $slot }}
    @endif

    @fluxScripts
</body>
</html>
