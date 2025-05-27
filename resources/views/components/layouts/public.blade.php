<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    @livewireStyles
</head>
<body class="min-h-screen">
    @hasSection('content')
        @yield('content')
    @else
        {{ $slot }}
    @endif

    @fluxScripts
    @livewireScripts
</body>
</html>
