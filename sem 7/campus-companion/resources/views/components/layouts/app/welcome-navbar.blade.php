<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --lime: #d2e823;
            --lime-muted: #b8cf10;
            --ink: #0c1018;
            --ink-soft: #161d2c;
            --ink-faint: #1f2b3e;
            --surface: #ffffff;
            --muted: rgba(255, 255, 255, 0.06);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--lime);
            color: var(--ink);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-display {
            font-family: 'Syne', sans-serif;
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar {
            width: 3px;
        }

        ::-webkit-scrollbar-track {
            background: var(--ink);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--lime);
            border-radius: 99px;
        }

        /* ── NAVBAR ── */
        .nav-root {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 200;
            padding: 1rem 1.25rem;
            transition: padding 0.35s ease, background 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
            border-bottom: 1px solid transparent;
        }

        @media (min-width: 768px) {
            .nav-root {
                padding: 1.5rem 2rem;
            }
        }

        .nav-root.scrolled {
            padding: 0.75rem 1.25rem;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.07);
            box-shadow: 0 4px 40px rgba(0, 0, 0, 0.07);
        }

        @media (min-width: 768px) {
            .nav-root.scrolled {
                padding: 0.85rem 2rem;
            }
        }

        /* Nav link */
        .nav-link {
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(12, 16, 24, 0.6);
            border-radius: 0.5rem;
            transition: color 0.2s, background 0.2s;
            text-decoration: none;
        }

        .nav-link:hover {
            color: var(--ink);
            background: rgba(0, 0, 0, 0.05);
        }

        /* CTA button */
        .nav-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            white-space: nowrap;
            padding: 0.6rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            background: var(--ink);
            color: var(--lime);
            border-radius: 99px;
            text-decoration: none;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
            border: none;
            cursor: pointer;
        }

        .nav-cta:hover {
            background: var(--ink-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22);
            transform: translateY(-1px);
        }

        .nav-ghost {
            padding: 0.6rem 1.1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--ink);
            background: transparent;
            border-radius: 99px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .nav-ghost:hover {
            background: rgba(0, 0, 0, 0.06);
        }

        /* Mobile menu */
        .mobile-drawer {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 1rem;
            right: 1rem;
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.14);
            border: 1px solid rgba(0, 0, 0, 0.06);
            padding: 1rem;
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .mobile-drawer.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        /* Hamburger bars */
        .ham-bar {
            display: block;
            width: 18px;
            height: 1.5px;
            background: var(--ink);
            border-radius: 99px;
            transition: transform 0.25s ease, opacity 0.2s;
            transform-origin: center;
        }

        /* ── SCROLL REVEAL ── */
        .reveal,
        .reveal-l,
        .reveal-r,
        .reveal-scale {
            transition: opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .reveal {
            opacity: 0;
            transform: translateY(40px);
        }

        .reveal-l {
            opacity: 0;
            transform: translateX(-50px);
        }

        .reveal-r {
            opacity: 0;
            transform: translateX(50px);
        }

        .reveal-scale {
            opacity: 0;
            transform: scale(0.92);
        }

        .reveal.in,
        .reveal-l.in,
        .reveal-r.in,
        .reveal-scale.in {
            opacity: 1;
            transform: translateY(0) translateX(0) scale(1);
        }

        /* Stagger helpers */
        .d-100 {
            transition-delay: 0.10s;
        }

        .d-200 {
            transition-delay: 0.20s;
        }

        .d-300 {
            transition-delay: 0.30s;
        }

        .d-400 {
            transition-delay: 0.40s;
        }

        .d-500 {
            transition-delay: 0.50s;
        }

        .d-600 {
            transition-delay: 0.60s;
        }

        /* ── GRID BG ── */
        .grid-bg {
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.055) 1px, transparent 1px);
            background-size: 56px 56px;
        }

        .grid-bg-light {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 56px 56px;
        }

        /* ── TICKER ── */
        @keyframes ticker {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .ticker-track {
            animation: ticker 28s linear infinite;
        }

        /* ── FLOAT ── */
        @keyframes floatA {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            40% {
                transform: translateY(-16px) rotate(1.5deg);
            }
        }

        @keyframes floatB {

            0%,
            100% {
                transform: translateY(0);
            }

            55% {
                transform: translateY(-22px);
            }
        }

        @keyframes floatC {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-10px) rotate(-2deg);
            }
        }

        .fa {
            animation: floatA 6s ease-in-out infinite;
        }

        .fb {
            animation: floatB 8s ease-in-out infinite 1.2s;
        }

        .fc {
            animation: floatC 7s ease-in-out infinite 0.6s;
        }

        /* ── ORB ── */
        @keyframes orbPulse {

            0%,
            100% {
                opacity: 0.45;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.08);
            }
        }

        .orb {
            animation: orbPulse 6s ease-in-out infinite;
        }

        /* ── CARD HOVER ── */
        .feature-card {
            background: var(--muted);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            padding: 1.75rem;
            transition: background 0.3s, border-color 0.3s, transform 0.3s;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(255, 255, 255, 0.14);
            transform: translateY(-4px);
        }

        /* Step connector */
        .step-connector {
            width: 1px;
            background: linear-gradient(to bottom, rgba(210, 232, 35, 0.5), transparent);
            height: 3rem;
            margin: 0.25rem auto;
        }

        /* FAQ item */
        .faq-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .faq-body {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .faq-body.open {
            max-height: 300px;
        }

        /* Gradient text */
        .text-lime {
            color: var(--lime);
        }

        /* Stat card */
        .stat-card {
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .stat-card:last-child {
            border-right: none;
        }

        /* Pill badge */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            border-radius: 99px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .pill-lime {
            background: rgba(210, 232, 35, 0.15);
            color: var(--lime);
            border: 1px solid rgba(210, 232, 35, 0.25);
        }

        .pill-dark {
            background: rgba(0, 0, 0, 0.12);
            color: var(--ink);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* CTA hover */
        .cta-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            background: var(--ink);
            color: var(--lime);
            border-radius: 99px;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            background: var(--ink-soft);
        }

        .cta-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            background: rgba(0, 0, 0, 0.08);
            color: var(--ink);
            border-radius: 99px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .cta-secondary:hover {
            background: rgba(0, 0, 0, 0.13);
        }

        .cta-primary-inv {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            background: var(--lime);
            color: var(--ink);
            border-radius: 99px;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .cta-primary-inv:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(210, 232, 35, 0.35);
        }
    </style>
</head>

<body x-data="{
    scrolled: false,
    mobileOpen: false,
    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 48;
        }, { passive: true });

        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('in');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal, .reveal-l, .reveal-r, .reveal-scale')
            .forEach(el => io.observe(el));
    }
}">

    <!-- ══ NAVBAR ══ -->
    <nav class="nav-root" :class="{ scrolled }">
        <div class="flex items-center justify-between max-w-7xl mx-auto">

            <!-- Logo -->
            <a href="/" class="flex items-center group flex-shrink-0">
                <img src="{{ asset('logos/logo.png') }}" alt="Campus Companion"
                    class="h-11 sm:h-12 md:h-13 max-h-14 w-auto object-contain group-hover:scale-105 transition-transform duration-200" />
            </a>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center gap-0.5">
                <a href="#features" class="nav-link">Features</a>
                <a href="#how-it-works" class="nav-link">How it works</a>
                <a href="#faq" class="nav-link">FAQ</a>
            </div>

            <!-- Desktop Auth & Theme -->
            <div class="hidden md:flex items-center gap-2">
                <x-theme-toggle />
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-cta inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="m-0 flex items-center">
                        @csrf
                        <button type="submit" class="nav-ghost" style="border:1px solid rgba(0,0,0,0.12);">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="nav-cta">Sign In →</a>
                @endguest
            </div>

            <!-- Mobile Hamburger -->
            <div class="md:hidden flex items-center gap-2">
                <x-theme-toggle />
                <button @click="mobileOpen = !mobileOpen"
                    class="w-9 h-9 flex flex-col justify-center items-center gap-[5px]
                           rounded-xl hover:bg-black/5 transition-colors"
                    aria-label="Menu">
                    <span class="ham-bar" :style="mobileOpen ? 'transform:rotate(45deg) translate(0, 6.5px)' : ''"></span>
                    <span class="ham-bar" :style="mobileOpen ? 'opacity:0' : ''"></span>
                    <span class="ham-bar" :style="mobileOpen ? 'transform:rotate(-45deg) translate(0, -6.5px)' : ''"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div class="mobile-drawer" :class="{ open: mobileOpen }">
            <div class="flex flex-col gap-1">
                <a href="#features" @click="mobileOpen=false" class="nav-link block">Features</a>
                <a href="#how-it-works" @click="mobileOpen=false" class="nav-link block">How it works</a>
                <a href="#faq" @click="mobileOpen=false" class="nav-link block">FAQ</a>
                <div class="h-px bg-black/6 my-2"></div>
                @guest
                    <a href="{{ route('login') }}" class="nav-cta flex items-center justify-center text-center mt-1">Sign In →</a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-cta flex items-center justify-center gap-2 text-center">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ══ SLOT ══ -->
    <main>{{ $slot }}</main>

    @fluxScripts
</body>

</html>
