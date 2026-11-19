<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50 dark:bg-[#0c1018] text-slate-900 dark:text-white antialiased">
    {{ $slot }}

    @vite(['resources/js/app.js'])
    @livewireScriptConfig
    @fluxScripts
</body>

</html>
