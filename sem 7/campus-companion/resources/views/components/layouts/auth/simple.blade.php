<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50 dark:bg-[#080c14] text-slate-900 dark:text-white antialiased transition-colors duration-200">

    <div class="fixed top-4 right-4 z-50">
        <x-theme-toggle />
    </div>

    <div class="flex min-h-svh items-center justify-center p-4 sm:p-6 md:p-10 w-full">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

    @vite(['resources/js/app.js'])
    @livewireScriptConfig
    @fluxScripts
</body>

</html>
