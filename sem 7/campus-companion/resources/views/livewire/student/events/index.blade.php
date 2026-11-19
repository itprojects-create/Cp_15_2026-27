<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;

return new #[Layout('components.layouts.student')] class extends Component {
    public function getEventsProperty()
    {
        $user = auth()->user();

        return Event::where(function ($q) use ($user) {
            $q->whereNull('department_id')->orWhere('department_id', $user->department_id);
        })
            ->orderBy('date')
            ->get();
    }
};
?>

<div class="min-h-screen space-y-8 pb-16">

    {{-- ── PAGE HEADER ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-8 shadow-sm dark:shadow-xl transition-all">
        <div class="absolute inset-0 opacity-10 dark:opacity-30 pointer-events-none"
            style="background-image: linear-gradient(currentColor 1px,transparent 1px),linear-gradient(90deg,currentColor 1px,transparent 1px); background-size:40px 40px; color: rgba(100,116,139,0.3);">
        </div>
        <div class="absolute -top-10 -left-10 w-56 h-56 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-semibold mb-4">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M16 3v4M8 3v4M3 10h18" />
                    </svg>
                    Campus Events
                </div>
                <h1 class="font-display font-extrabold text-4xl text-slate-900 dark:text-white tracking-tight">Events</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">Discover what's happening on campus</p>
            </div>

            <div class="flex items-center gap-2 bg-slate-50 dark:bg-blue-500/10 border border-slate-200 dark:border-blue-500/20 rounded-2xl px-4 py-3 shrink-0">
                <span class="font-display font-bold text-3xl text-blue-600 dark:text-blue-400 leading-none">{{ $this->events->where('date', '>=', now()->toDateString())->count() }}</span>
                <span class="text-slate-500 dark:text-slate-400 text-xs leading-tight">Upcoming<br>Events</span>
            </div>
        </div>
    </div>

    {{-- ── EVENT GRID ── --}}
    <div class="grid md:grid-cols-2 gap-4">

        @forelse($this->events as $i => $e)
            @php
                $isPast = \Carbon\Carbon::parse($e->date)->isPast();
                $isToday = \Carbon\Carbon::parse($e->date)->isToday();
                $isSoon = \Carbon\Carbon::parse($e->date)->diffInDays(now()) <= 3 && !$isPast;
            @endphp

            <div class="group relative bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/10 transition-all duration-200 flex flex-col gap-5 overflow-hidden shadow-sm dark:shadow-none {{ $isPast ? 'opacity-50' : '' }}"
                style="animation: fadeUp 0.4s ease both; animation-delay: {{ $i * 0.06 }}s">
                {{-- Background date watermark --}}
                <div
                    class="absolute -right-3 -bottom-3 font-display font-extrabold text-[5rem] leading-none text-slate-900/[0.03] dark:text-white/[0.02] select-none pointer-events-none">
                    {{ \Carbon\Carbon::parse($e->date)->format('d') }}
                </div>

                {{-- TOP --}}
                <div class="flex items-start justify-between gap-3">

                    {{-- Date block --}}
                    <div
                        class="flex-shrink-0 w-14 h-14 rounded-xl bg-slate-100 dark:bg-[#080c14] border border-slate-200 dark:border-white/8 flex flex-col items-center justify-center">
                        <span class="font-display font-bold text-xl text-lime-600 dark:text-[#d2e823] leading-none">
                            {{ \Carbon\Carbon::parse($e->date)->format('d') }}
                        </span>
                        <span class="text-[0.55rem] text-slate-500 font-semibold uppercase tracking-wider mt-0.5">
                            {{ \Carbon\Carbon::parse($e->date)->format('M') }}
                        </span>
                    </div>

                    {{-- Status badge --}}
                    @if ($isToday)
                        <span
                            class="px-2.5 py-1 rounded-full bg-lime-500/15 border border-lime-500/25 text-lime-700 dark:text-[#d2e823] text-[0.65rem] font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></span>Today
                        </span>
                    @elseif($isSoon)
                        <span
                            class="px-2.5 py-1 rounded-full bg-amber-500/15 border border-amber-500/25 text-amber-600 dark:text-amber-400 text-[0.65rem] font-bold uppercase tracking-wider">Soon</span>
                    @elseif($isPast)
                        <span
                            class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-white/5 text-slate-500 text-[0.65rem] font-semibold uppercase tracking-wider">Past</span>
                    @endif
                </div>

                {{-- CONTENT --}}
                <div class="flex-1">
                    <h2 class="font-semibold text-lg text-slate-900 dark:text-white/90 group-hover:text-slate-950 dark:group-hover:text-white transition leading-snug">
                        {{ $e->title }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed line-clamp-2">
                        {{ $e->description }}
                    </p>
                </div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-white/5">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ $e->location }}
                    </div>
                    <span class="text-xs text-slate-400 dark:text-slate-500">
                        {{ \Carbon\Carbon::parse($e->date)->diffForHumans() }}
                    </span>
                </div>

            </div>

        @empty

            <div class="col-span-2 flex flex-col items-center justify-center py-24 text-center">
                <div
                    class="w-16 h-16 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/5 flex items-center justify-center mb-4 shadow-sm dark:shadow-none">
                    <svg class="w-7 h-7 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                    </svg>
                </div>
                <p class="text-slate-600 dark:text-slate-400 text-sm font-medium">No events scheduled</p>
                <p class="text-slate-400 dark:text-slate-600 text-xs mt-1">Check back soon for upcoming events</p>
            </div>
        @endforelse

    </div>

</div>

<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
