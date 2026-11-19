<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>Campus Companion - Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logos/cropped-primary-32x32.png') }}">
    <link rel="shortcut icon" href="{{ asset('logos/cropped-primary-32x32.png') }}">

    <script>
        if (localStorage.getItem('theme') === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>

    @vite('resources/css/app.css')
    @livewireStyles

    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --lime: #d2e823;
            --lime-dim: rgba(210, 232, 35, 0.12);
            --ink: #0c1018;
            --bg: #080c14;
            --surface: #0b1120;
            --surface-2: #0f1929;
            --border: rgba(255, 255, 255, 0.06);
            --text-muted: #64748b;
        }

        html.light, [data-theme="light"] {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-2: #f1f5f9;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        html.light body {
            color: #0f172a;
        }

        .font-display {
            font-family: 'Syne', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            z-index: 60;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease;
        }

        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: 100%;
            min-width: 0;
            transition: margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            }
            .sidebar.drawer-open {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            .main-wrapper {
                margin-left: 250px;
            }
        }

        .topbar {
            height: 60px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 40;
            backdrop-filter: blur(12px);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
    </style>
</head>

<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">

    <!-- Backdrop for Mobile & iPad -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 lg:hidden"
         x-cloak></div>

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="sidebar flex flex-col justify-between px-3 py-6"
               :class="{ 'drawer-open': sidebarOpen }">

            <div>

                <!-- LOGO & CLOSE (mobile) -->
                <div class="px-3 mb-8 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-block">
                        <img src="{{ asset('logos/logo.png') }}" alt="VESIT" class="h-9 w-auto object-contain" />
                    </a>
                    <button @click="sidebarOpen = false"
                            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition"
                            aria-label="Close Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                @php
                    $base = 'flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm font-medium';
                    $active = 'bg-[#d2e823] text-black font-bold shadow-sm';
                    $inactive = 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5';
                @endphp

                <!-- NAVIGATION -->
                <nav class="space-y-1" @click="if (window.innerWidth < 1024) sidebarOpen = false">

                    @if(auth()->user()->role === 'admin')
                    <!-- Dashboard -->
                    <a href="/admin/dashboard" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/dashboard') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M3 12l9-9 9 9M4 10v10h6v-6h4v6h6V10" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Departments -->
                    <a href="/admin/departments" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/departments*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M3 21h18M4 21V7l8-4 8 4v14" />
                        </svg>
                        Departments
                    </a>
                    @endif

                    <!-- Classes -->
                    <a href="/admin/classes" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/classes*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path d="M12 14l6.16-3.42M12 14v7" />
                        </svg>
                        Classes
                    </a>

                    <!-- Timetable -->
                    <div class="space-y-1">
                        <a href="/admin/timetable" wire:navigate
                            class="{{ $base }} {{ request()->is('admin/timetable') ? $active : $inactive }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <path d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                            Timetable
                        </a>

                        <div class="pl-7 pr-2 space-y-1">
                            <a href="/admin/timetable/master" wire:navigate
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->is('admin/timetable/master*') ? 'bg-[#d2e823]/20 text-lime-600 dark:text-[#d2e823]' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->is('admin/timetable/master*') ? 'bg-lime-500 dark:bg-[#d2e823]' : 'bg-slate-400' }}"></span>
                                Master Grid
                            </a>
                            <a href="/admin/timetable/import" wire:navigate
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request()->is('admin/timetable/import*') ? 'bg-[#d2e823]/20 text-lime-600 dark:text-[#d2e823]' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->is('admin/timetable/import*') ? 'bg-lime-500 dark:bg-[#d2e823]' : 'bg-slate-400' }}"></span>
                                Excel Import
                            </a>
                        </div>
                    </div>

                    <!-- Campus Map & Floors -->
                    <a href="/admin/map" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/map*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3V6z" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 3v15" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15 6v15" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Campus Map
                    </a>

                    @if(auth()->user()->role === 'admin')
                    <!-- Notices -->
                    <a href="/admin/notices" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/notices*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path
                                d="M15 17h5l-1.5-1.5A2 2 0 0118 14V9a6 6 0 10-12 0v5c0 .53-.21 1.04-.59 1.41L4 17h5" />
                        </svg>
                        Notices
                    </a>

                    <!-- Events -->
                    <a href="/admin/events" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/events*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <path d="M8 2v4M16 2v4M3 10h18" />
                        </svg>
                        Events
                    </a>

                    <!-- Users -->
                    <a href="/admin/users" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/users*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M16 14a4 4 0 10-8 0M12 14v7M5 21h14" />
                        </svg>
                        Users
                    </a>

                    <!-- Complaints -->
                    <a href="/admin/complaints" wire:navigate
                        class="{{ $base }} {{ request()->is('admin/complaints*') ? $active : $inactive }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16v12H4z" />
                        </svg>
                        Complaints
                    </a>
                    @endif

                </nav>

            </div>

            <!-- FOOTER -->
            <div class="px-3 text-xs text-slate-500">
                © {{ date('Y') }} Campus Companion
            </div>

        </aside>

        <!-- MAIN -->
        <div class="main-wrapper w-full flex flex-col min-w-0">

            <!-- TOPBAR -->
            <header class="topbar flex justify-between items-center px-4 sm:px-6">

                <div class="flex items-center gap-2.5 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 -ml-1 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition flex items-center justify-center shrink-0"
                            aria-label="Toggle navigation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">
                        {{ auth()->user()->role === 'timetable_commity' ? 'Timetable Committee' : 'Admin Portal' }}
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">

                    @if(auth()->user()->role === 'admin')
                    <a href="/admin/users/create" wire:navigate
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#d2e823] text-black hover:opacity-90 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Create User
                    </a>
                    @endif

                    <!-- Theme Toggle -->
                    <x-theme-toggle />

                    <!-- User Profile & Avatar -->
                    <div class="flex items-center gap-2 sm:gap-2.5 pl-2 sm:pl-3 border-l border-slate-200 dark:border-white/10">
                        <x-user-avatar size="sm" />
                        <div class="hidden md:block text-left">
                            <div class="text-xs font-semibold text-slate-900 dark:text-white leading-tight truncate max-w-[120px]">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="text-[0.65rem] text-lime-600 dark:text-[#d2e823] font-bold uppercase tracking-wider">
                                {{ auth()->user()->role === 'timetable_commity' ? 'Committee' : 'Admin' }}
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button class="px-2 sm:px-2.5 py-1.5 rounded-lg text-xs text-red-500 hover:text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-500/10 transition font-medium">
                            Logout
                        </button>
                    </form>

                </div>

            </header>

            <!-- CONTENT -->
            <main class="flex-1 p-3.5 sm:p-5 lg:p-7 min-w-0 overflow-x-hidden">
                {{ $slot }}
            </main>

        </div>

    </div>

    @livewireScripts
</body>

</html>
