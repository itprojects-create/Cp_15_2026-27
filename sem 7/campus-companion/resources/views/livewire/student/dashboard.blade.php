<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Notice;
use App\Models\Event;

return new #[Layout('components.layouts.student')] class extends Component {

    public string $title = 'Dashboard';
    public $notices;
    public $events;

    public function mount()
    {
        $user = auth()->user();

        // Notices filtered by class OR department OR global
        $this->notices = Notice::query()
            ->where(function ($q) use ($user) {
                $q->where(function ($sub) {
                    $sub->whereNull('department_id')->whereNull('class_room_id');
                });

                if ($user->department_id) {
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->where('department_id', $user->department_id)
                            ->whereNull('class_room_id');
                    });
                }

                if ($user->class_room_id) {
                    $q->orWhere('class_room_id', $user->class_room_id);
                }
            })
            ->latest()
            ->take(5)
            ->get();

        // Events filtered by department OR global
        $this->events = Event::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('department_id')
                  ->orWhere('department_id', $user->department_id);
            })
            ->orderBy('date')
            ->take(5)
            ->get();
    }
};
?>

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<style>
    body { font-family: 'DM Sans', sans-serif; -webkit-font-smoothing: antialiased; }
    .font-display { font-family: 'Syne', sans-serif; }
    .card-shimmer { position: relative; overflow: hidden; }
    .card-shimmer::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg, transparent 40%, rgba(210,232,35,0.06) 50%, transparent 60%);
        opacity: 0;
        transition: opacity 0.4s;
        border-radius: inherit;
    }
    .card-shimmer:hover::after { opacity: 1; }
    .dash-grid-bg {
        background-image:
            linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px);
        background-size: 48px 48px;
    }
    html.dark .dash-grid-bg {
        background-image:
            linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    }
</style>
@endpush

<div class="min-h-screen dash-grid-bg space-y-7 py-2">

    {{-- ── PAGE HEADER / WELCOME CARD ── --}}
    <div class="flex flex-wrap items-center justify-between gap-6 p-6 rounded-3xl bg-white dark:bg-gradient-to-r dark:from-[#0f172a]/90 dark:via-[#0b1120] dark:to-[#080c14] border border-slate-200 dark:border-white/5 shadow-sm dark:shadow-xl transition-all">
        <div class="flex items-center gap-4">
            <x-user-avatar size="lg" class="shadow-lg ring-2 ring-[#d2e823]/40 shrink-0" />
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-slate-100 dark:bg-white/[0.05] border border-slate-200 dark:border-white/[0.08] text-[0.7rem] text-slate-600 dark:text-slate-400 font-medium mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></span>
                    {{ now()->format('l, d M Y') }}
                </div>
                <h1 class="font-display font-extrabold text-slate-900 dark:text-white leading-tight" style="font-size:clamp(1.5rem,3.5vw,2.2rem)">
                    Welcome back, <span class="text-lime-600 dark:text-[#d2e823]">{{ auth()->user()->name }}</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">
                    {{ auth()->user()->department?->name ?? 'VESIT' }}
                    @if(auth()->user()->classRoom)
                        • {{ auth()->user()->classRoom->year }} - {{ auth()->user()->classRoom->name }}
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-100 dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.08]">
            <span class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400 animate-pulse"></span>
            <span class="text-xs text-slate-700 dark:text-white/70 font-semibold uppercase tracking-wider">Student Active</span>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Notices --}}
        <div class="card-shimmer bg-white dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] rounded-2xl p-5 group hover:border-amber-500/30 hover:-translate-y-0.5 transition-all duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/15 flex items-center justify-center group-hover:bg-amber-500/15 group-hover:border-amber-500/30 transition-colors">
                    <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-[0.65rem] text-green-600 dark:text-green-400 bg-green-500/10 border border-green-500/20 px-2 py-0.5 rounded-full font-semibold">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 10l7-7 7 7"/></svg>
                    Live
                </div>
            </div>
            <p class="font-display font-extrabold text-lime-600 dark:text-[#d2e823] leading-none" style="font-size:2.75rem">{{ $notices->count() }}</p>
            <p class="text-slate-500 dark:text-white/40 text-xs font-medium mt-2 uppercase tracking-wider">Total Notices</p>
        </div>

        {{-- Events --}}
        <div class="card-shimmer bg-white dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] rounded-2xl p-5 group hover:border-blue-500/30 hover:-translate-y-0.5 transition-all duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/15 flex items-center justify-center group-hover:bg-blue-500/15 group-hover:border-blue-500/30 transition-colors">
                    <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 10h18"/>
                    </svg>
                </div>
                <div class="text-[0.65rem] text-blue-600 dark:text-blue-400/70 font-semibold">This period</div>
            </div>
            <p class="font-display font-extrabold text-blue-600 dark:text-blue-400 leading-none" style="font-size:2.75rem">{{ $events->count() }}</p>
            <p class="text-slate-500 dark:text-white/40 text-xs font-medium mt-2 uppercase tracking-wider">Upcoming Events</p>
        </div>

        {{-- Role --}}
        <div class="card-shimmer bg-white dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] rounded-2xl p-5 group hover:border-[#d2e823]/40 hover:-translate-y-0.5 transition-all duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-[#d2e823]/10 border border-[#d2e823]/20 flex items-center justify-center group-hover:bg-[#d2e823]/20 transition-colors">
                    <svg class="w-5 h-5 text-lime-600 dark:text-[#d2e823]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M12 2l7 3v5c0 5-3 9-7 11C8 19 5 15 5 10V5l7-3z"/>
                    </svg>
                </div>
                <div class="text-[0.65rem] text-slate-500 dark:text-white/30 font-medium">Verified</div>
            </div>
            <p class="font-display font-extrabold text-slate-900 dark:text-slate-100 leading-none capitalize" style="font-size:1.85rem">{{ auth()->user()->role }}</p>
            <p class="text-slate-500 text-xs font-medium mt-2 uppercase tracking-wider">Access Level</p>
        </div>

    </div>

    {{-- ── MAIN GRID: Notices + Events ── --}}
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- NOTICES PANEL --}}
        <div class="bg-white dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] rounded-2xl overflow-hidden shadow-sm dark:shadow-none">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-white/[0.05]">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <span class="font-display font-semibold text-slate-900 dark:text-white text-[0.9rem]">Latest Notices</span>
                </div>
                <a href="/student/notices"
                   class="inline-flex items-center gap-1 text-[0.72rem] text-lime-600 dark:text-[#d2e823]/60 hover:text-lime-700 dark:hover:text-[#d2e823] transition-colors font-medium">
                    View all
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- List --}}
            <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                @forelse($notices as $notice)
                    <div class="group flex items-start gap-3.5 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/[0.03] transition-colors cursor-default">
                        <div class="w-1.5 h-1.5 rounded-full bg-[#d2e823] dark:bg-[#d2e823]/40 mt-[7px] flex-shrink-0 group-hover:bg-lime-500 dark:group-hover:bg-[#d2e823] transition-colors"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 dark:text-white/75 text-sm font-medium leading-snug group-hover:text-slate-900 dark:group-hover:text-white transition-colors truncate">
                                {{ $notice->title }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[0.62rem] text-slate-400 dark:text-white/25">{{ $notice->created_at->diffForHumans() }}</span>
                                @if($notice->category)
                                    <span class="text-[0.6rem] px-1.5 py-0.5 rounded-full font-semibold capitalize
                                        @if($notice->category === 'academic') bg-blue-500/15 text-blue-600 dark:text-blue-400
                                        @elseif($notice->category === 'administrative') bg-purple-500/15 text-purple-600 dark:text-purple-400
                                        @else bg-green-500/15 text-green-600 dark:text-green-400 @endif">
                                        {{ $notice->category }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-14">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-slate-400 dark:text-white/15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <p class="text-slate-400 dark:text-white/25 text-sm">No notices available</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- EVENTS PANEL --}}
        <div class="bg-white dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] rounded-2xl overflow-hidden shadow-sm dark:shadow-none">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-white/[0.05]">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M16 3v4M8 3v4M3 10h18"/>
                        </svg>
                    </div>
                    <span class="font-display font-semibold text-slate-900 dark:text-white text-[0.9rem]">Upcoming Events</span>
                </div>
                <a href="/student/events"
                   class="inline-flex items-center gap-1 text-[0.72rem] text-lime-600 dark:text-[#d2e823]/60 hover:text-lime-700 dark:hover:text-[#d2e823] transition-colors font-medium">
                    View all
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- List --}}
            <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                @forelse($events as $event)
                    <div class="group flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-white/[0.03] transition-colors cursor-default">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-[0.6rem] font-bold text-blue-600 dark:text-blue-400 uppercase leading-none">{{ \Carbon\Carbon::parse($event->date)->format('M') }}</span>
                            <span class="font-display font-bold text-sm text-slate-900 dark:text-white leading-none mt-0.5">{{ \Carbon\Carbon::parse($event->date)->format('d') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 dark:text-white/75 text-sm font-medium leading-snug group-hover:text-slate-900 dark:group-hover:text-white transition-colors truncate">
                                {{ $event->title }}
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[0.62rem] text-slate-400 dark:text-white/25">{{ \Carbon\Carbon::parse($event->date)->format('h:i A') }}</span>
                                @if($event->location)
                                    <span class="text-[0.62rem] text-slate-400 dark:text-white/25">• {{ $event->location }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-14">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/[0.04] border border-slate-200 dark:border-white/[0.07] flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-slate-400 dark:text-white/15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                <path d="M16 3v4M8 3v4M3 10h18"/>
                            </svg>
                        </div>
                        <p class="text-slate-400 dark:text-white/25 text-sm">No upcoming events</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>

</div>
