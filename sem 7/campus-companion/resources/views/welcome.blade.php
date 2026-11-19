<x-layouts.app.welcome-navbar>

    {{-- ════════════════════════════════════════════
         § 1  HERO
    ════════════════════════════════════════════ --}}
    <section class="relative min-h-[90vh] lg:min-h-screen bg-[#d2e823] grid-bg flex items-center overflow-hidden pt-24 sm:pt-28 md:pt-32 pb-16 sm:pb-20 px-4 sm:px-6 md:px-8">

        {{-- Background orbs --}}
        <div
            class="orb absolute top-[-10%] right-[-8%] w-[520px] h-[520px] rounded-full
                    bg-gradient-to-br from-[#f0fa6e] to-[#b8cf10] opacity-50 blur-[90px] pointer-events-none">
        </div>
        <div class="orb absolute bottom-[-15%] left-[-5%] w-[400px] h-[400px] rounded-full
                    bg-gradient-to-tr from-[#d2e823] to-[#8aaf00] opacity-40 blur-[80px] pointer-events-none"
            style="animation-delay:2s"></div>

        <div class="relative max-w-7xl mx-auto w-full">
            <div class="flex flex-col lg:flex-row items-center gap-10 sm:gap-12 lg:gap-16">

                {{-- ── LEFT ── --}}
                <div class="flex-1 space-y-5 sm:space-y-7 max-w-2xl w-full">

                    {{-- Badge --}}
                    <div class="pill pill-dark inline-flex reveal d-100 max-w-full text-xs sm:text-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse shrink-0"></span>
                        <span class="truncate">Built for VESIT, University of Mumbai</span>
                    </div>

                    {{-- Headline --}}
                    <h1 class="font-display font-extrabold text-[#0c1018] leading-[1.05] sm:leading-[0.95] tracking-tight reveal d-200 break-words"
                        style="font-size: clamp(2.15rem, 7.5vw, 5.5rem);">
                        Your campus.<br>
                        <span class="relative inline-block">
                            Reimagined.
                            <svg class="absolute -bottom-1.5 sm:-bottom-2 left-0 w-full" viewBox="0 0 420 18" fill="none"
                                preserveAspectRatio="none">
                                <path d="M4 14 C80 4, 210 4, 416 10" stroke="#0c1018" stroke-width="4"
                                    stroke-linecap="round" opacity="0.35" />
                            </svg>
                        </span>
                    </h1>

                    <p class="text-[#0c1018]/70 text-base sm:text-lg leading-relaxed max-w-lg reveal d-300">
                        Notices, events, timetable, and complaints — unified in one intelligent platform.
                        Stop chasing noticeboards.
                    </p>

                    {{-- CTAs --}}
                    <div class="flex flex-wrap gap-3 reveal d-400">
                        @guest
                            <a href="{{ route('login') }}" class="cta-primary">
                                Sign in to portal
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}" class="cta-primary">
                                Go to Dashboard
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endauth
                    </div>

                    {{-- Trust line --}}
                    <p class="text-[#0c1018]/50 text-xs sm:text-sm reveal d-500 font-medium">
                        No setup fees · Role-based access · Works on every device
                    </p>
                </div>

                {{-- ── RIGHT — Floating UI Cards ── --}}
                <div
                    class="flex-1 relative flex justify-center items-center min-h-[380px] sm:min-h-[440px] w-full max-w-full lg:max-w-[520px] lg:ml-8 reveal-r d-200 mt-4 lg:mt-0">
 
                    {{-- Notice card —float A --}}
                    <div
                        class="fa absolute top-0 left-2 sm:left-12 lg:left-24 z-10 bg-white rounded-2xl shadow-xl shadow-black/10 p-3.5 sm:p-4 w-44 sm:w-52">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path
                                        d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <span class="text-[0.65rem] sm:text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider">New Notice</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800 leading-snug">Mid-semester exam schedule</p>
                        <p class="text-[0.65rem] text-slate-400 mt-1">IT Dept · 2 mins ago</p>
                        <div class="mt-2.5 h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-1.5 w-3/4 rounded-full bg-amber-400"></div>
                        </div>
                    </div>
 
                    {{-- Event card — float B --}}
                    <div
                        class="fb absolute top-6 right-1 sm:-right-4 lg:-right-8 z-20 bg-[#0c1018] rounded-2xl shadow-xl shadow-black/20 p-3.5 sm:p-4 w-48 sm:w-56">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <span
                                class="text-[0.65rem] font-bold text-[#d2e823] uppercase tracking-widest">Upcoming</span>
                            <span class="text-[0.65rem] text-white/40">Mar 14</span>
                        </div>
                        <p class="text-xs sm:text-sm font-bold text-white leading-snug">TechFest 2025 — Registration Open</p>
                        <div class="flex items-center gap-2 mt-2.5 sm:mt-3">
                            <div class="flex -space-x-1.5">
                                <div
                                    class="w-5 h-5 rounded-full bg-blue-500 border border-[#0c1018] text-[0.5rem] flex items-center justify-center text-white font-bold">
                                    A</div>
                                <div
                                    class="w-5 h-5 rounded-full bg-purple-500 border border-[#0c1018] text-[0.5rem] flex items-center justify-center text-white font-bold">
                                    K</div>
                                <div
                                    class="w-5 h-5 rounded-full bg-rose-500 border border-[#0c1018] text-[0.5rem] flex items-center justify-center text-white font-bold">
                                    R</div>
                            </div>
                            <span class="text-[0.65rem] text-white/40">+128 registered</span>
                        </div>
                    </div>
 
                    {{-- Timetable card — float C --}}
                    <div
                        class="fc absolute bottom-4 sm:bottom-10 left-2 sm:left-8 lg:left-20 z-10 bg-white rounded-2xl shadow-xl shadow-black/10 p-3.5 sm:p-4 w-44 sm:w-48">
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider mb-2">Today's Classes</p>
                        <div class="space-y-1.5 sm:space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-6 sm:h-7 rounded-full bg-[#d2e823]"></div>
                                <div>
                                    <p class="text-[0.68rem] sm:text-[0.7rem] font-bold text-slate-800">Data Structures</p>
                                    <p class="text-[0.58rem] sm:text-[0.6rem] text-slate-400">9:00 – 10:00 AM</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-6 sm:h-7 rounded-full bg-blue-400"></div>
                                <div>
                                    <p class="text-[0.68rem] sm:text-[0.7rem] font-bold text-slate-800">DBMS Lab</p>
                                    <p class="text-[0.58rem] sm:text-[0.6rem] text-slate-400">11:00 AM – 1:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    {{-- Complaint resolved badge — center --}}
                    <div class="relative z-30 bg-white rounded-3xl shadow-2xl shadow-black/12 p-4 sm:p-6 w-56 sm:w-64 ml-auto mr-2 lg:mr-4">
                        <div class="flex items-center gap-2.5 sm:gap-3 mb-2.5 sm:mb-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-2xl bg-green-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-slate-800">Complaint Resolved</p>
                                <p class="text-[0.65rem] text-slate-400">Ref #CC-2041</p>
                            </div>
                        </div>
                        <p class="text-[0.68rem] sm:text-[0.72rem] text-slate-600 leading-relaxed">
                            "Projector fault in Lab 3 has been fixed. Thanks for reporting."
                        </p>
                        <div class="mt-2.5 sm:mt-3 flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-[0.58rem] sm:text-[0.6rem] text-green-600 font-bold">Resolved by IT Support</span>
                        </div>
                    </div>
 
                </div>
            </div>
        </div>
    </section>


    {{-- ════════════════════════════════════════════
         § 2  MARQUEE TICKER
    ════════════════════════════════════════════ --}}
    <div class="bg-[#0c1018] py-4 overflow-hidden border-y border-white/5">
        <div class="flex ticker-track whitespace-nowrap select-none" aria-hidden="true">
            @php
                $ticks = [
                    'Smart Notices',
                    'Live Timetable',
                    'Event Discovery',
                    'Complaint Tracking',
                    'Role-based Access',
                    'Real-time Updates',
                    'VESIT Mumbai',
                    'One Dashboard',
                    'Smart Notices',
                    'Live Timetable',
                    'Event Discovery',
                    'Complaint Tracking',
                    'Role-based Access',
                    'Real-time Updates',
                    'VESIT Mumbai',
                    'One Dashboard',
                ];
            @endphp
            @foreach ($ticks as $tick)
                <span class="inline-flex items-center gap-3 px-6 text-sm font-medium text-white/40">
                    <span class="w-1 h-1 rounded-full bg-[#d2e823] flex-shrink-0"></span>
                    {{ $tick }}
                </span>
            @endforeach
        </div>
    </div>


    {{-- ════════════════════════════════════════════
         § 3  FEATURES
    ════════════════════════════════════════════ --}}
    <section id="features" class="bg-[#0c1018] grid-bg-light text-white py-28 px-6">
        <div class="max-w-7xl mx-auto">

            <div class="mb-16 reveal">
                <div class="pill pill-lime mb-5">Platform Features</div>
                <h2 class="font-display font-bold text-white leading-tight"
                    style="font-size: clamp(2rem, 5vw, 3.25rem);">
                    Everything campus life<br>needs. In one place.
                </h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">

                {{-- Notices --}}
                <div class="feature-card reveal d-100">
                    <div class="text-[#d2e823]/30 font-display font-extrabold text-5xl mb-4 leading-none select-none">
                        01</div>
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-2">Smart Notices</h3>
                    <p class="text-white/40 text-sm leading-relaxed">
                        Real-time notices with categories, search, and instant department filtering.
                    </p>
                </div>

                {{-- Events --}}
                <div class="feature-card reveal d-200">
                    <div class="text-[#d2e823]/30 font-display font-extrabold text-5xl mb-4 leading-none select-none">
                        02</div>
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M16 3v4M8 3v4M3 10h18" />
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-2">Events</h3>
                    <p class="text-white/40 text-sm leading-relaxed">
                        Discover, register, and track campus events. Never miss TechFest again.
                    </p>
                </div>

                {{-- Timetable --}}
                <div class="feature-card reveal d-300">
                    <div class="text-[#d2e823]/30 font-display font-extrabold text-5xl mb-4 leading-none select-none">
                        03</div>
                    <div
                        class="w-10 h-10 rounded-xl bg-lime-500/10 border border-lime-500/20 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-2">Timetable</h3>
                    <p class="text-white/40 text-sm leading-relaxed">
                        Your weekly schedule, always up to date. Know what's next before you forget.
                    </p>
                </div>

                {{-- Complaints --}}
                <div class="feature-card reveal d-400">
                    <div class="text-[#d2e823]/30 font-display font-extrabold text-5xl mb-4 leading-none select-none">
                        04</div>
                    <div
                        class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M14.7 6.3a4 4 0 11-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-2">Complaints</h3>
                    <p class="text-white/40 text-sm leading-relaxed">
                        Raise issues, auto-notify the right department, and track resolution status.
                    </p>
                </div>

            </div>
        </div>
    </section>


    {{-- ════════════════════════════════════════════
         § 4  STATS STRIP
    ════════════════════════════════════════════ --}}
    <div class="bg-[#111820] border-y border-white/5">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4">
            @php
                $stats = [
                    ['2000+', 'Students enrolled'],
                    ['50+', 'Daily notices'],
                    ['20+', 'Active events'],
                    ['98%', 'Issue resolution rate'],
                ];
            @endphp
            @foreach ($stats as $i => $stat)
                <div class="stat-card py-10 px-8 text-center reveal" style="transition-delay: {{ $i * 0.1 }}s">
                    <div class="font-display font-extrabold text-4xl text-[#d2e823] mb-1.5 leading-none">
                        {{ $stat[0] }}
                    </div>
                    <div class="text-white/40 text-sm">{{ $stat[1] }}</div>
                </div>
            @endforeach
        </div>
    </div>


    {{-- ════════════════════════════════════════════
         § 5  HOW IT WORKS
    ════════════════════════════════════════════ --}}
    <section id="how-it-works" class="bg-[#0c1018] py-28 px-6">
        <div class="max-w-4xl mx-auto">

            <div class="text-center mb-16 reveal">
                <div class="pill pill-lime mb-5">Simple by design</div>
                <h2 class="font-display font-bold text-white" style="font-size: clamp(2rem, 5vw, 3rem);">
                    Up and running in minutes
                </h2>
            </div>

            <div class="relative">
                {{-- Vertical line --}}
                <div
                    class="absolute left-6 md:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-[#d2e823]/30 via-[#d2e823]/10 to-transparent md:-translate-x-px">
                </div>

                <div class="space-y-12">
                    @php
                        $steps = [
                            [
                                '01',
                                'Create your account',
                                'Sign up with your college email. Your role — student, teacher, or admin — determines your access level automatically.',
                                'bg-blue-500/10 border-blue-500/20 text-blue-400',
                            ],
                            [
                                '02',
                                'Explore your dashboard',
                                'See your personalized feed: today\'s classes, latest department notices, upcoming events, and open complaints — all at a glance.',
                                'bg-[#d2e823]/10 border-[#d2e823]/20 text-[#d2e823]',
                            ],
                            [
                                '03',
                                'Stay ahead',
                                'Get notified instantly when something new hits your department. Raise complaints in 30 seconds. Check who\'s playing in the next sports meet.',
                                'bg-rose-500/10 border-rose-500/20 text-rose-400',
                            ],
                        ];
                    @endphp

                    @foreach ($steps as $i => $step)
                        <div
                            class="relative flex flex-col md:flex-row items-start gap-6 {{ $i % 2 === 1 ? 'md:flex-row-reverse' : '' }} reveal">
                            {{-- Step number bubble --}}
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full bg-[#0c1018] border-2 border-[#d2e823]/40
                                        flex items-center justify-center z-10 relative ml-0 md:ml-0
                                        font-display font-bold text-sm text-[#d2e823]">
                                {{ $step[0] }}
                            </div>
                            {{-- Card --}}
                            <div class="flex-1 feature-card {{ $i % 2 === 1 ? 'md:mr-8' : 'md:ml-8' }}"
                                style="margin-top: 0;">
                                <div
                                    class="w-8 h-8 rounded-lg border {{ $step[3] }} flex items-center justify-center mb-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        @if ($i === 0)
                                            <path
                                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z" />
                                        @elseif($i === 1)
                                            <rect x="3" y="3" width="7" height="7" rx="1" />
                                            <rect x="14" y="3" width="7" height="7" rx="1" />
                                            <rect x="3" y="14" width="7" height="7" rx="1" />
                                            <rect x="14" y="14" width="7" height="7" rx="1" />
                                        @else
                                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        @endif
                                    </svg>
                                </div>
                                <h3 class="font-display font-semibold text-white text-lg mb-2">{{ $step[1] }}
                                </h3>
                                <p class="text-white/40 text-sm leading-relaxed">{{ $step[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>


    {{-- ════════════════════════════════════════════
    § 6  BUILT FOR REAL CAMPUSES (split panel)
════════════════════════════════════════════ --}}
    <section class="bg-[#d2e823] grid-bg py-28 px-6">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">

            {{-- ── LEFT ── --}}
            <div class="flex-1 space-y-8 reveal-l">
                <div class="pill pill-dark">Role-based platform</div>

                <h2 class="font-display font-bold text-[#0c1018] leading-tight"
                    style="font-size: clamp(2rem, 4.5vw, 3.25rem);">
                    Built for real<br>campuses.
                </h2>

                <p class="text-[#0c1018]/60 text-lg leading-relaxed max-w-md">
                    Students see their schedule. Teachers post notices. Admins manage everything.
                    One platform, three perspectives.
                </p>

                {{-- Role Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 pt-2">

                    {{-- Student --}}
                    <div
                        class="bg-white rounded-2xl p-5 text-[#0c1018] shadow-sm
                           hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#d2e823]/60 flex items-center justify-center mb-3
                               group-hover:bg-[#d2e823] transition-colors">
                            <svg class="w-5 h-5 text-[#0c1018]" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                {{-- Graduation cap --}}
                                <path d="M12 3L2 8l10 5 10-5-10-5z" />
                                <path d="M2 8v6c0 0 3 4 10 4s10-4 10-4V8" />
                                <path d="M22 8v5" />
                                <circle cx="22" cy="14" r="1" fill="currentColor" stroke="none" />
                            </svg>
                        </div>
                        <div class="text-sm font-bold tracking-tight">Student</div>
                        <div class="text-[0.65rem] text-[#0c1018]/45 mt-0.5 leading-snug">
                            View schedule &amp; notices
                        </div>
                    </div>

                    {{-- Teacher --}}
                    <div
                        class="bg-[#0c1018] rounded-2xl p-5 text-[#d2e823] shadow-sm
                           hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#d2e823]/10 flex items-center justify-center mb-3
                               group-hover:bg-[#d2e823]/20 transition-colors">
                            <svg class="w-5 h-5 text-[#d2e823]" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                {{-- Chalkboard / presentation board --}}
                                <rect x="2" y="3" width="20" height="14" rx="2" />
                                <path d="M8 21h8M12 17v4" />
                                <path d="M7 8h4M7 11h6" />
                                <circle cx="16" cy="9.5" r="2.5" />
                            </svg>
                        </div>
                        <div class="text-sm font-bold tracking-tight">Teacher</div>
                        <div class="text-[0.65rem] text-[#d2e823]/45 mt-0.5 leading-snug">
                            Post notices &amp; events
                        </div>
                    </div>

                    {{-- Admin --}}
                    <div
                        class="bg-white/65 rounded-2xl p-5 text-[#0c1018] shadow-sm
                           hover:shadow-md hover:bg-white hover:-translate-y-0.5 transition-all duration-200 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#0c1018]/8 flex items-center justify-center mb-3
                               group-hover:bg-[#0c1018]/14 transition-colors">
                            <svg class="w-5 h-5 text-[#0c1018]" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                {{-- Shield with cog / admin badge --}}
                                <path d="M12 2l7 3v5c0 5-3 9-7 11C8 19 5 15 5 10V5l7-3z" />
                                <circle cx="12" cy="11" r="2.5" />
                                <path d="M12 8.5V9M12 13v.5M9.5 11H10M14 11h.5
                                    M10.27 9.27l.35.35M13.38 12.38l.35.35
                                    M10.27 12.73l.35-.35M13.38 9.62l.35-.35" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="text-sm font-bold tracking-tight">Admin</div>
                        <div class="text-[0.65rem] text-[#0c1018]/45 mt-0.5 leading-snug">
                            Full system control
                        </div>
                    </div>

                </div>

                {{-- Permission legend --}}
                <div class="flex flex-col gap-2 pt-1">
                    @php
                        $perms = [
                            ['View notices & timetable', true, true, true],
                            ['Post notices & events', false, true, true],
                            ['Raise complaints', true, false, true],
                            ['Manage users & roles', false, false, true],
                        ];
                    @endphp
                    @foreach ($perms as $perm)
                        <div class="flex items-center gap-3 text-[0.72rem]">
                            <span class="flex-1 text-[#0c1018]/55 font-medium">{{ $perm[0] }}</span>
                            @foreach (array_slice($perm, 1) as $has)
                                @if ($has)
                                    <span
                                        class="w-5 h-5 rounded-full bg-[#0c1018] flex items-center justify-center flex-shrink-0">
                                        <svg class="w-2.5 h-2.5 text-[#d2e823]" fill="none" stroke="currentColor"
                                            stroke-width="3" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="w-5 h-5 rounded-full bg-[#0c1018]/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-2.5 h-2.5 text-[#0c1018]/25" fill="none"
                                            stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path d="M18 6L6 18M6 6l12 12" />
                                        </svg>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                    {{-- Column headers --}}
                    <div class="flex items-center gap-3 mt-1">
                        <span class="flex-1"></span>
                        <span
                            class="w-5 text-[0.55rem] text-center text-[#0c1018]/35 font-semibold uppercase tracking-wider">Stu</span>
                        <span
                            class="w-5 text-[0.55rem] text-center text-[#0c1018]/35 font-semibold uppercase tracking-wider">Tea</span>
                        <span
                            class="w-5 text-[0.55rem] text-center text-[#0c1018]/35 font-semibold uppercase tracking-wider">Adm</span>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT — Dashboard Mockup ── --}}
            <div class="flex-1 reveal-r">
                <div class="bg-[#0c1018] rounded-3xl p-6 shadow-2xl shadow-black/20">

                    {{-- Window chrome --}}
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-rose-500/70"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500/70"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/70"></div>
                        </div>
                        <div class="flex items-center gap-1.5 bg-white/5 rounded-full px-3 py-1">
                            <svg class="w-2.5 h-2.5 text-white/25" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span class="text-[0.6rem] text-white/20 font-mono">campus-companion.vesit.edu</span>
                        </div>
                        <div class="w-12"></div>
                    </div>

                    {{-- Stat cards --}}
                    <div class="grid grid-cols-2 gap-3 mb-3">

                        <div
                            class="bg-white/5 rounded-xl p-3.5 border border-white/6 group
                               hover:bg-white/8 transition-colors">
                            <div class="flex items-center gap-1.5 mb-2">
                                <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path
                                        d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span
                                    class="text-[0.6rem] text-white/30 uppercase tracking-wider font-semibold">Notices</span>
                            </div>
                            <div class="font-display font-bold text-[#d2e823] text-2xl leading-none">14</div>
                            <div class="flex items-center gap-1 mt-1.5">
                                <svg class="w-2.5 h-2.5 text-green-400" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M5 10l7-7 7 7M12 3v18" />
                                </svg>
                                <span class="text-[0.62rem] text-green-400">3 new today</span>
                            </div>
                        </div>

                        <div
                            class="bg-white/5 rounded-xl p-3.5 border border-white/6
                               hover:bg-white/8 transition-colors">
                            <div class="flex items-center gap-1.5 mb-2">
                                <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="16" rx="2" />
                                    <path d="M16 3v4M8 3v4M3 10h18" />
                                </svg>
                                <span
                                    class="text-[0.6rem] text-white/30 uppercase tracking-wider font-semibold">Events</span>
                            </div>
                            <div class="font-display font-bold text-blue-400 text-2xl leading-none">6</div>
                            <div class="flex items-center gap-1 mt-1.5">
                                <svg class="w-2.5 h-2.5 text-white/30" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
                                <span class="text-[0.62rem] text-white/30">This week</span>
                            </div>
                        </div>

                    </div>

                    {{-- Timetable --}}
                    <div class="bg-white/5 rounded-xl p-3.5 border border-white/6 mb-3">
                        <div class="flex items-center gap-1.5 mb-3">
                            <svg class="w-3 h-3 text-white/30" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                            <span class="text-[0.6rem] text-white/30 uppercase tracking-wider font-semibold">Today's
                                Schedule</span>
                        </div>
                        <div class="space-y-2">
                            @php
                                $classes = [
                                    ['Data Structures', '9:00 AM', 'bg-[#d2e823]', 'text-[#0c1018]', 'Lec'],
                                    ['DBMS Lab', '11:00 AM', 'bg-blue-400', 'text-white', 'Lab'],
                                    ['OS Theory', '2:00 PM', 'bg-rose-400', 'text-white', 'Lec'],
                                ];
                            @endphp
                            @foreach ($classes as $class)
                                <div class="flex items-center gap-2.5">
                                    <div class="w-1 h-6 rounded-full {{ $class[2] }} flex-shrink-0"></div>
                                    <span
                                        class="text-[0.72rem] text-white/70 flex-1 font-medium">{{ $class[0] }}</span>
                                    <span
                                        class="text-[0.58rem] px-1.5 py-0.5 rounded bg-white/6 text-white/30 font-mono">{{ $class[4] }}</span>
                                    <span
                                        class="text-[0.62rem] text-white/25 w-14 text-right">{{ $class[1] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Complaints --}}
                    <div class="bg-white/5 rounded-xl p-3.5 border border-white/6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-7 h-7 rounded-lg bg-rose-500/10 border border-rose-500/20
                                       flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M14.7 6.3a4 4 0 11-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4z" />
                                    </svg>
                                </div>
                                <div>
                                    <div
                                        class="text-[0.6rem] text-white/30 uppercase tracking-wider font-semibold mb-0.5">
                                        Open Complaints
                                    </div>
                                    <div class="text-sm font-semibold text-white leading-none">2 active</div>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1 bg-green-500/10 border border-green-500/20
                                   rounded-full px-2.5 py-1">
                                <svg class="w-2.5 h-2.5 text-green-400" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <span class="text-[0.62rem] text-green-400 font-semibold">1 resolved today</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>


    {{-- ════════════════════════════════════════════
         § 7  FAQ
    ════════════════════════════════════════════ --}}
    <section id="faq" class="bg-[#0c1018] py-28 px-6">
        <div class="max-w-3xl mx-auto">

            <div class="text-center mb-14 reveal">
                <div class="pill pill-lime mb-5">FAQ</div>
                <h2 class="font-display font-bold text-white" style="font-size: clamp(2rem, 4vw, 2.75rem);">
                    Questions? Answered.
                </h2>
            </div>

            @php
                $faqs = [
                    [
                        'What is Campus Companion?',
                        'A centralized digital platform for VESIT that unifies notices, events, timetable, and complaint management — replacing scattered WhatsApp groups and physical noticeboards.',
                    ],
                    [
                        'Who can use it?',
                        'Students, teachers, and administrators. Each role gets a different view and permission set. Students can read and raise complaints; teachers can post notices; admins control everything.',
                    ],
                    [
                        'Is it free?',
                        'Yes, Campus Companion is free for all VESIT students and staff. Just sign up with your college email.',
                    ],
                    [
                        'How do complaints work?',
                        'Submit a complaint, select the relevant department, and it auto-routes to the right staff. You get notified when the status changes — pending, in-progress, or resolved.',
                    ],
                    [
                        'Can I access it on mobile?',
                        'Absolutely. The platform is fully responsive and optimized for all screen sizes.',
                    ],
                ];
            @endphp

            <div class="space-y-0 reveal">
                @foreach ($faqs as $i => $faq)
                    <div x-data="{ open: false }" class="faq-item">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between py-5 text-left group">
                            <span
                                class="text-white font-medium text-[0.95rem] group-hover:text-[#d2e823] transition-colors pr-6">
                                {{ $faq[0] }}
                            </span>
                            <div class="flex-shrink-0 w-6 h-6 rounded-full border border-white/10 flex items-center justify-center transition-colors"
                                :class="open ? 'bg-[#d2e823] border-[#d2e823]' : 'group-hover:border-white/30'">
                                <svg class="w-3 h-3 transition-transform duration-300"
                                    :class="open ? 'rotate-45 text-[#0c1018]' : 'text-white/50'" fill="none"
                                    stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                            </div>
                        </button>
                        <div class="faq-body" :class="{ open }">
                            <p class="text-white/40 text-sm leading-relaxed pb-5">{{ $faq[1] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    {{-- ════════════════════════════════════════════
         § 8  CTA
    ════════════════════════════════════════════ --}}
    <section class="bg-[#d2e823] grid-bg py-28 px-6 relative overflow-hidden">

        <div class="absolute inset-0 bg-gradient-to-br from-[#e8f83a] via-[#d2e823] to-[#b8cf10] pointer-events-none">
        </div>

        <div class="relative max-w-3xl mx-auto text-center reveal">

            <div class="pill pill-dark mb-7">
                VESIT, University of Mumbai
            </div>

            <h2 class="font-display font-extrabold text-[#0c1018] leading-tight mb-5"
                style="font-size: clamp(2.25rem, 5vw, 3.75rem);">
                Start managing your<br>campus smarter today.
            </h2>

            <p class="text-[#0c1018]/55 text-lg mb-10 max-w-xl mx-auto">
                Join hundreds of VESIT students and staff already using Campus Companion.
            </p>

            <div class="flex flex-wrap gap-4 justify-center">
                @guest
                    <a href="{{ route('login') }}" class="cta-primary">
                        Sign in to Campus Companion
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="cta-primary">
                        Open Dashboard →
                    </a>
                @endauth
            </div>

            <p class="text-[#0c1018]/35 text-xs mt-8 tracking-wide">
                No credit card. No setup. Works immediately.
            </p>

        </div>
    </section>


    {{-- ════════════════════════════════════════════
         § 9  FOOTER
    ════════════════════════════════════════════ --}}
    <footer class="bg-[#0c1018] border-t border-white/5 py-10 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">

            <div class="flex items-center">
                <a href="/" class="inline-block">
                    <img src="{{ asset('logos/logo.png') }}" alt="VESIT" class="h-9 w-auto object-contain brightness-95 hover:brightness-100 transition" />
                </a>
            </div>

            <p class="text-white/20 text-xs text-center">
                Built for VESIT, University of Mumbai · {{ date('Y') }}
            </p>

            <div class="flex items-center gap-5">
                <a href="#features" class="text-white/25 hover:text-white/60 text-xs transition-colors">Features</a>
                <a href="#how-it-works" class="text-white/25 hover:text-white/60 text-xs transition-colors">How it
                    works</a>
                <a href="#faq" class="text-white/25 hover:text-white/60 text-xs transition-colors">FAQ</a>
            </div>

        </div>
    </footer>

</x-layouts.app.welcome-navbar>
