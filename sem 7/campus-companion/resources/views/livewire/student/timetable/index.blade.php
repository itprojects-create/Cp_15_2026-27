<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Timetable;

return new #[Layout('components.layouts.student')] class extends Component {
    public $timetable = [];
    public $activeDay = '';

    public function mount()
    {
        $this->activeDay = now()->format('l');

        if (!in_array($this->activeDay, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])) {
            $this->activeDay = 'Monday';
        }

        $user = auth()->user();

        if (!$user->class_room_id) {
            $this->timetable = [];
            return;
        }

        $data = Timetable::with('faculty')
            ->where('class_room_id', $user->class_room_id)
            ->get();

        $slots = ['08:30-09:30', '09:30-10:30', '10:30-11:30', '11:30-12:30', '12:30-01:30', '01:30-02:30', '02:30-03:30'];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $timetable = [];

        // create full grid
        foreach ($slots as $slot) {
            foreach ($days as $day) {
                $timetable[$slot][$day] = null;
            }
        }

        // fill data
        foreach ($data as $row) {
            $normalizedSlot = Timetable::normalizeSlot($row->time_slot);
            $slotKey = isset($timetable[$row->time_slot]) ? $row->time_slot : (isset($timetable[$normalizedSlot]) ? $normalizedSlot : $row->time_slot);
            $timetable[$slotKey][$row->day] = [
                'subject' => $row->subject,
                'room' => $row->room,
                'faculty' => $row->faculty?->name ?? '—',
            ];
        }

        $this->timetable = $timetable;
    }

    public function setDay($day)
    {
        $this->activeDay = $day;
    }
};
?>

<div class="min-h-screen space-y-8 pb-16">

    {{-- ── PAGE HEADER ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-8 shadow-sm dark:shadow-xl transition-all">
        <div class="absolute inset-0 opacity-10 dark:opacity-30 pointer-events-none"
            style="background-image: linear-gradient(currentColor 1px,transparent 1px),linear-gradient(90deg,currentColor 1px,transparent 1px); background-size:40px 40px; color: rgba(100,116,139,0.3);">
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-[#d2e823]/5 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-500/10 border border-lime-500/20 text-lime-600 dark:text-[#d2e823] text-xs font-semibold mb-4">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    Weekly Schedule
                </div>
                <h1 class="font-display font-extrabold text-4xl text-slate-900 dark:text-white tracking-tight">Timetable</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">Your class schedule for the week</p>
            </div>

            {{-- Today indicator --}}
            <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/8 rounded-2xl px-4 py-3 shrink-0">
                <div class="w-2 h-2 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></div>
                <span class="text-slate-600 dark:text-white/60 text-sm">Today is <span
                        class="text-slate-900 dark:text-white font-semibold">{{ now()->format('l') }}</span></span>
            </div>
        </div>
    </div>

    {{-- ── DAY TABS (mobile-friendly) ── --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
            @php $isToday = now()->format('l') === $day; @endphp
            <button wire:click="setDay('{{ $day }}')"
                class="flex-shrink-0 flex flex-col items-center px-5 py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm dark:shadow-none
                    {{ $activeDay === $day
                        ? 'bg-[#d2e823] text-black font-extrabold border border-lime-400 shadow-sm'
                        : 'bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/8 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-white/20' }}">
                <span
                    class="text-[0.6rem] font-bold uppercase tracking-widest {{ $activeDay === $day ? 'opacity-70' : 'opacity-50' }}">
                    {{ substr($day, 0, 3) }}
                </span>
                <span>{{ substr($day, 0, 3) }}</span>
                @if ($isToday)
                    <span
                        class="w-1.5 h-1.5 rounded-full mt-1 {{ $activeDay === $day ? 'bg-black/60' : 'bg-lime-500 dark:bg-[#d2e823]' }}"></span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ── MOBILE/CARD VIEW for active day ── --}}
    <div class="space-y-3">

        @forelse($timetable as $time => $days)
            @if ($time === '12:30-01:30')
                {{-- BREAK --}}
                <div class="flex items-center gap-4 py-2">
                    <div class="text-right w-24 flex-shrink-0">
                        <span class="text-xs text-slate-500 dark:text-slate-500 font-mono">{{ $time }}</span>
                    </div>
                    <div class="flex-1 h-px bg-gradient-to-r from-amber-500/30 to-transparent"></div>
                    <span
                        class="text-[0.65rem] font-bold text-amber-600 dark:text-yellow-500/80 uppercase tracking-widest flex-shrink-0">Lunch
                        Break</span>
                    <div class="flex-1 h-px bg-gradient-to-l from-amber-500/30 to-transparent"></div>
                </div>
                @continue
            @endif

            @php
                $cell = $days[$activeDay] ?? null;
                // Color palette cycling
                $colors = [
                    ['bg-lime-500/10', 'border-lime-500/20', 'text-lime-700 dark:text-[#d2e823]', 'bg-lime-500 dark:bg-[#d2e823]'],
                    ['bg-blue-500/10', 'border-blue-500/20', 'text-blue-600 dark:text-blue-400', 'bg-blue-500 dark:bg-blue-400'],
                    ['bg-purple-500/10', 'border-purple-500/20', 'text-purple-600 dark:text-purple-400', 'bg-purple-500 dark:bg-purple-400'],
                    ['bg-rose-500/10', 'border-rose-500/20', 'text-rose-600 dark:text-rose-400', 'bg-rose-500 dark:bg-rose-400'],
                    ['bg-amber-500/10', 'border-amber-500/20', 'text-amber-600 dark:text-amber-400', 'bg-amber-500 dark:bg-amber-400'],
                    ['bg-cyan-500/10', 'border-cyan-500/20', 'text-cyan-600 dark:text-cyan-400', 'bg-cyan-500 dark:bg-cyan-400'],
                ];
                $ci = array_search($time, array_keys($timetable)) % count($colors);
                $c = $colors[$ci];
            @endphp

            <div class="flex items-stretch gap-4"
                style="animation: fadeUp 0.3s ease both; animation-delay: {{ $loop->index * 0.04 }}s">

                {{-- Time label --}}
                <div class="text-right w-24 flex-shrink-0 flex flex-col justify-center">
                    <span
                        class="text-xs text-slate-500 font-mono leading-tight">{{ str_replace('-', '–', $time) }}</span>
                </div>

                {{-- Class card or empty --}}
                @if ($cell && $cell['subject'] !== 'BREAK')
                    <div
                        class="flex-1 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/10 transition-all duration-200 group relative overflow-hidden shadow-sm dark:shadow-none">

                        {{-- Accent line --}}
                        <div class="absolute left-0 top-3 bottom-3 w-1 rounded-full {{ $c[3] }}"></div>

                        <div class="pl-3 flex items-start justify-between gap-4">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white/90 group-hover:text-slate-950 dark:group-hover:text-white transition">
                                    {{ $cell['subject'] }}
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-1">{{ $cell['faculty'] }}</p>
                            </div>

                            <span
                                class="flex-shrink-0 px-2.5 py-1 rounded-lg {{ $c[0] }} border {{ $c[1] }} {{ $c[2] }} text-xs font-semibold">
                                Room {{ $cell['room'] }}
                            </span>
                        </div>
                    </div>
                @else
                    <div
                        class="flex-1 border border-dashed border-slate-200 dark:border-white/5 rounded-xl flex items-center justify-center py-3">
                        <span class="text-slate-400 dark:text-slate-700 text-sm">—</span>
                    </div>
                @endif

            </div>

        @empty
            <div class="text-center py-16 text-slate-500 text-sm">No schedule available</div>
        @endforelse

    </div>

    {{-- ── FULL WEEK TABLE (desktop) ── --}}
    <div class="hidden xl:block bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl overflow-hidden mt-8 shadow-sm dark:shadow-none">

        <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5">
            <h2 class="font-display font-bold text-slate-700 dark:text-white/60 text-xs uppercase tracking-widest">Full Week View</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#080c14]">
                        <th class="p-3.5 text-left text-xs text-slate-500 font-semibold uppercase tracking-wider w-28">
                            Time</th>
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                            <th
                                class="p-3.5 text-center text-xs font-semibold uppercase tracking-wider
                                {{ now()->format('l') === $day ? 'text-lime-600 dark:text-[#d2e823] font-bold' : 'text-slate-500' }}">
                                {{ substr($day, 0, 3) }}
                                @if (now()->format('l') === $day)
                                    <span class="block w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-[#d2e823] mx-auto mt-1"></span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($timetable as $time => $days)
                        @if ($time === '12:30-01:30')
                            <tr>
                                <td colspan="6"
                                    class="px-4 py-3 bg-amber-500/5 dark:bg-yellow-500/5 text-amber-700 dark:text-yellow-500/80 text-xs font-semibold uppercase tracking-widest border-y border-amber-500/15 dark:border-yellow-500/10">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-yellow-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                        </svg>
                                        <span>Lunch Break — {{ $time }}</span>
                                    </div>
                                </td>
                            </tr>
                            @continue
                        @endif
                        <tr>
                            <td class="p-3 text-xs text-slate-500 font-mono whitespace-nowrap">
                                {{ str_replace('-', '–', $time) }}</td>
                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                @php $cell = $days[$day] ?? null; @endphp
                                <td class="p-2">
                                    @if ($cell && $cell['subject'] !== 'BREAK')
                                        <div
                                            class="bg-slate-50 dark:bg-[#080c14] border border-slate-200/80 dark:border-white/5 rounded-xl p-2.5 hover:bg-slate-100 dark:hover:bg-[#0c1324] transition text-center shadow-xs">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white/80 leading-snug">
                                                {{ $cell['subject'] }}</p>
                                            <p class="text-[0.6rem] text-blue-600 dark:text-blue-400 font-medium mt-0.5">{{ $cell['faculty'] }}</p>
                                            <p class="text-[0.55rem] text-slate-500 dark:text-slate-600 mt-0.5 font-medium">Room {{ $cell['room'] }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="text-center text-slate-300 dark:text-slate-700">—</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
