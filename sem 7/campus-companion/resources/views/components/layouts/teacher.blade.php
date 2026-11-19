<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>Campus Companion</title>
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
    @stack('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap"
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
            --border: rgba(255, 255, 255, 0.055);
            --text-muted: #64748b;
        }

        html.light, [data-theme="light"] {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-2: #f1f5f9;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
        }

        html {
            scroll-behavior: smooth;
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

        ::-webkit-scrollbar {
            width: 3px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--lime);
            border-radius: 99px;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 248px;
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 60;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease;
        }

        .main-wrapper {
            flex: 1;
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
            .main-content {
                padding: 1.25rem 1rem !important;
            }
            .topbar {
                padding: 0 1rem !important;
            }
        }

        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            .main-wrapper {
                margin-left: 248px;
            }
        }

        .sidebar-logo {
            padding: 1.4rem 1.5rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .logo-mark {
            width: 32px;
            height: 32px;
            background: var(--lime);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-mark svg {
            width: 14px;
            height: 14px;
        }

        .logo-text {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
            line-height: 1.1;
        }

        .logo-sub {
            font-size: 0.65rem;
            color: var(--text-muted);
            font-weight: 400;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-section-label {
            font-size: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 0.5rem 0.75rem 0.25rem;
            margin-top: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.6rem 0.875rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            transition: background 0.18s, color 0.18s;
            position: relative;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #0f172a;
        }

        html.dark .nav-item:hover,
        [data-theme="dark"] .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
        }

        .nav-item.active {
            background: var(--lime);
            color: #000;
            font-weight: 700;
        }

        .nav-item.active .nav-dot {
            opacity: 1;
        }

        .nav-icon {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.75;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 0.75rem;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
        }

        html.dark .user-card,
        [data-theme="dark"] .user-card {
            background: rgba(255, 255, 255, 0.03);
        }

        .user-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--lime);
            color: var(--ink);
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-family: 'Syne', sans-serif;
        }

        .user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #0f172a;
        }

        html.dark .user-name,
        [data-theme="dark"] .user-name {
            color: #e2e8f0;
        }

        .user-role {
            font-size: 0.65rem;
            color: var(--text-muted);
        }

        /* ── TOPBAR ── */
        .topbar {
            height: 60px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: #94a3b8;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            color: #64748b;
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
            text-decoration: none;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }

        .icon-btn svg {
            width: 15px;
            height: 15px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.75;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .logout-btn {
            padding: 0.45rem 0.875rem;
            font-size: 0.8rem;
            font-weight: 500;
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.18s;
            font-family: 'DM Sans', sans-serif;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.18);
        }

        /* ── ROLE BADGE in topbar ── */
        .role-badge {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.25rem 0.6rem;
            border-radius: 99px;
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.2);
        }

        .layout-root {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 1.75rem 2rem;
        }

        .notif-wrap {
            position: relative;
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fbbf24;
            border: 1.5px solid var(--bg);
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

    <div class="layout-root">

        <!-- ══ SIDEBAR ══ -->
        <aside class="sidebar flex flex-col justify-between"
               :class="{ 'drawer-open': sidebarOpen }">

            <div>
                <div class="sidebar-logo flex items-center justify-between px-4 py-4">
                    <a href="{{ url('/') }}" class="inline-block">
                        <img src="{{ asset('logos/logo.png') }}" alt="VESIT" style="height:36px; width:auto; object-fit:contain;" />
                    </a>
                    <button @click="sidebarOpen = false"
                            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition"
                            aria-label="Close Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <nav class="sidebar-nav" @click="if (window.innerWidth < 1024) sidebarOpen = false">

                    <span class="nav-section-label">Overview</span>

                    <a href="/teacher/dashboard" class="nav-item {{ request()->is('teacher/dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5" />
                            <rect x="14" y="3" width="7" height="7" rx="1.5" />
                            <rect x="14" y="14" width="7" height="7" rx="1.5" />
                            <rect x="3" y="14" width="7" height="7" rx="1.5" />
                        </svg>
                        Dashboard
                    </a>

                    <span class="nav-section-label">Manage</span>

                    <a href="/teacher/notices"
                        class="nav-item post-action {{ request()->is('teacher/notices*') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1" />
                        </svg>
                        Notices
                    </a>

                    <a href="/teacher/events"
                        class="nav-item post-action {{ request()->is('teacher/events*') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        Events
                    </a>

                    <a href="/teacher/timetable" class="nav-item {{ request()->is('teacher/timetable*') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                        Timetable
                    </a>

                    <a href="/teacher/map" class="nav-item {{ request()->is('teacher/map*') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3V6z" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 3v15" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15 6v15" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Campus Map
                    </a>

                    <a href="/teacher/complaints"
                        class="nav-item {{ request()->is('teacher/complaints*') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                        </svg>
                        Complaints
                    </a>

                    <span class="nav-section-label">Account</span>

                    <a href="/teacher/profile" class="nav-item {{ request()->is('teacher/profile*') ? 'active' : '' }}">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="7" r="4" />
                            <path d="M5.5 21a6.5 6.5 0 0113 0" />
                        </svg>
                        Profile
                    </a>

                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="user-card">
                    @if (auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                            class="user-avatar-sm object-cover shrink-0" />
                    @else
                        <div class="user-avatar-sm shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <div class="user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ auth()->user()->name ?? 'Teacher' }}
                        </div>
                        <div class="user-role">Faculty</div>
                    </div>
                </div>
            </div>

        </aside>

        <!-- ══ MAIN ══ -->
        <div class="main-wrapper w-full flex flex-col min-w-0">

            <header class="topbar flex justify-between items-center px-4 sm:px-6">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 -ml-1 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition flex items-center justify-center shrink-0"
                            aria-label="Toggle navigation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <span class="topbar-title truncate">
                        @php
                            $segments = request()->segments();
                            echo implode(' › ', array_map('ucfirst', array_slice($segments, 1)));
                        @endphp
                    </span>
                    <span class="role-badge hidden sm:inline-block">Faculty</span>
                </div>

                <div class="topbar-actions flex items-center gap-2 sm:gap-3 shrink-0">

                    <div class="notif-wrap">
                        <x-notification-bell />
                    </div>

                    <!-- Theme Toggle -->
                    <x-theme-toggle />

                    <!-- User Profile & Avatar -->
                    <div class="flex items-center gap-2 sm:gap-2.5 pl-2 sm:pl-3 border-l border-slate-200 dark:border-white/10">
                        <x-user-avatar size="sm" />
                        <div class="hidden md:block text-left">
                            <div class="text-xs font-semibold text-slate-900 dark:text-white leading-tight truncate max-w-[120px]">
                                {{ auth()->user()->name ?? 'Teacher' }}
                            </div>
                            <div class="text-[0.65rem] text-lime-600 dark:text-[#d2e823] font-bold uppercase tracking-wider">
                                Faculty
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-btn px-2 sm:px-2.5 py-1.5">Logout</button>
                    </form>

                </div>
            </header>

            <main class="main-content flex-1 p-3.5 sm:p-5 lg:p-7 min-w-0 overflow-x-hidden">
                {{ $slot }}
            </main>

        </div>
    </div>

    @livewireScripts
</body>

</html>
