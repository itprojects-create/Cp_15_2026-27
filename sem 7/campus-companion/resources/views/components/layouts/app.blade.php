<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="bg-slate-50 dark:bg-[#0c1018] text-slate-900 dark:text-white font-sans antialiased min-h-screen">

@php
    $role = auth()->user()->role;

    $base = match($role) {
        'admin' => '/admin',
        'teacher' => '/teacher',
        default => '/student'
    };

    $active = fn($path) => request()->is(ltrim($path, '/').'*')
        ? 'bg-[#d2e823] text-black font-bold shadow-sm'
        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5';
@endphp

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <!-- BACKDROP for Mobile -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 lg:hidden"
         x-cloak></div>

    <!-- SIDEBAR -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:static top-0 left-0 bottom-0 w-64 bg-white dark:bg-[#080c14] border-r border-slate-200 dark:border-white/5 z-50 flex flex-col transition-transform duration-300 ease-in-out h-full">

        <!-- LOGO -->
        <div class="px-6 py-5 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('logos/logo.png') }}" alt="VESIT" class="h-9 w-auto object-contain" />
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- NAV -->
        <nav class="flex-1 px-4 py-6 space-y-1 text-sm overflow-y-auto">

            <!-- DASHBOARD -->
            <a href="{{ $base }}/dashboard"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/dashboard') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>

            @if($role === 'admin')
                <a href="{{ $base }}/notices"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/notices') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5"/>
                        <path d="M9 17v1a3 3 0 006 0v-1"/>
                    </svg>
                    Notices
                </a>

                <a href="{{ $base }}/events"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/events') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 10h18"/>
                    </svg>
                    Events
                </a>

                <a href="{{ $base }}/users"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/users') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                    Users
                </a>

            @elseif($role === 'teacher')
                <a href="{{ $base }}/timetable"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/timetable') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                    Timetable
                </a>

                <a href="{{ $base }}/complaints"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/complaints') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    Complaints
                </a>

            @else
                <a href="{{ $base }}/timetable"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/timetable') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    Timetable
                </a>

                <a href="{{ $base }}/notices"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/notices') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5"/>
                    </svg>
                    Notices
                </a>

                <a href="{{ $base }}/events"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/events') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                    Events
                </a>

                <a href="{{ $base }}/complaints"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ $active($base.'/complaints') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    Complaints
                </a>

            @endif

        </nav>

    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- HEADER -->
        <header class="h-16 border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 bg-white dark:bg-[#080c14] sticky top-0 z-40">

            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 -ml-1 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-center shrink-0"
                        aria-label="Toggle navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <h2 class="text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 capitalize truncate">
                    {{ $role }} Panel
                </h2>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0">

                <x-notification-bell />
                <x-theme-toggle />

                <span class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white hidden sm:inline-block">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="text-red-500 hover:text-red-600 dark:text-red-400 text-xs font-semibold px-2 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                        Logout
                    </button>
                </form>

            </div>

        </header>

        <!-- CONTENT -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            {{ $slot }}
        </main>

    </div>

</div>

@livewireScripts
</body>
</html>
