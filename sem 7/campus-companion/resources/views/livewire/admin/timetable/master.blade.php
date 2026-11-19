<?php

use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\Timetable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    #[Url]
    public $department_id = null;

    public $departments;
    public $selectedDay = 'Monday';
    public $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    public $timeSlots = [
        '08:30-09:30',
        '09:30-10:30',
        '10:30-11:30',
        '11:30-12:30',
        '12:30-01:30',
        '01:30-02:30',
        '02:30-03:30',
    ];

    public function mount()
    {
        $this->departments = Department::all();

        if (!$this->department_id && $this->departments->isNotEmpty()) {
            $userDept = auth()->user()->department_id;
            $this->department_id = $userDept ?: $this->departments->first()->id;
        }
    }

    public function setDay($day)
    {
        $this->selectedDay = $day;
    }

    public function with(): array
    {
        $classes = collect();
        $grid = [];

        if ($this->department_id) {
            $classes = ClassRoom::where('department_id', $this->department_id)
                ->orderBy('year')
                ->orderBy('name')
                ->get();

            $classIds = $classes->pluck('id')->toArray();

            $timetables = Timetable::with('faculty')
                ->whereIn('class_room_id', $classIds)
                ->where('day', $this->selectedDay)
                ->get();

            // Populate Grid: [time_slot][class_id] => Timetable object or null
            foreach ($this->timeSlots as $slot) {
                $stdSlot = str_replace('–', '-', $slot);
                foreach ($classes as $class) {
                    $item = $timetables->first(function ($t) use ($stdSlot, $class) {
                        return $t->class_room_id === $class->id &&
                               (str_replace('–', '-', $t->time_slot) === $stdSlot ||
                                ltrim($t->time_slot, '0') === ltrim($stdSlot, '0'));
                    });
                    $grid[$slot][$class->id] = $item;
                }
            }
        }

        return [
            'classes' => $classes,
            'grid' => $grid,
            'currentDepartment' => Department::find($this->department_id),
        ];
    }
};
?>

<div class="space-y-8 max-w-full pb-16">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="/admin/timetable" wire:navigate class="text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Timetable
                </a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs font-semibold text-blue-600 dark:text-[#d2e823]">Master Department View</span>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Department Master Timetable</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Cross-class master schedule showing all years and divisions side-by-side.</p>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <a href="/admin/timetable/import" wire:navigate class="px-4 py-2.5 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Import Excel
            </a>
            <a href="/admin/timetable" wire:navigate class="px-4 py-2.5 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                Single Class View
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-[#d2e823] hover:opacity-90 active:scale-[0.98] text-black text-xs font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.023-.641-2.5-1.42-3.829C4.52 8.653 3.75 7.6 3.75 6a3.75 3.75 0 017.5 0c0 1.6-.77 2.653-1.55 3.999-.779 1.33-1.18 2.806-1.42 3.83m6.72-10.329c.24 1.023.641 2.5 1.42 3.829.78 1.346 1.55 2.4 1.55 4a3.75 3.75 0 01-7.5 0c0-1.6.77-2.653 1.55-3.999.779-1.33 1.18-2.806 1.42-3.83M3 20.25h18" />
                </svg>
                Print Master TT
            </button>
        </div>
    </div>

    <!-- Filters Bar: Department & Day Selection -->
    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-5 shadow-sm dark:shadow-none flex flex-col md:flex-row md:items-center justify-between gap-4">

        <!-- Department Switcher -->
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 whitespace-nowrap">Department:</span>
            <select wire:model.live="department_id" class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm font-bold text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Day Selection Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
            @foreach ($days as $day)
                <button wire:click="setDay('{{ $day }}')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-150 whitespace-nowrap
                    {{ $selectedDay === $day ? 'bg-[#d2e823] text-black shadow-sm' : 'bg-slate-50 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10' }}">
                    {{ $day }}
                </button>
            @endforeach
        </div>

    </div>

    <!-- MASTER TIMETABLE GRID -->
    @if ($classes->isEmpty())
        <div class="p-12 text-center bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl space-y-3">
            <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">No classes or timetable records found for {{ $currentDepartment?->name ?? 'this department' }}.</p>
            <a href="/admin/timetable/import" wire:navigate class="inline-block px-5 py-2.5 rounded-xl bg-[#d2e823] text-black text-xs font-bold hover:opacity-90 transition">
                Upload Master Excel
            </a>
        </div>
    @else
        <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl overflow-hidden shadow-sm dark:shadow-none">

            <!-- Title banner inside print / table view -->
            <div class="p-4 bg-slate-50 dark:bg-[#0b1120] border-b border-slate-200 dark:border-white/10 flex items-center justify-between flex-wrap gap-2">
                <div class="font-extrabold text-sm uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-lime-500 dark:bg-[#d2e823]"></span>
                    {{ $currentDepartment?->name }} &mdash; Master Timetable &mdash; {{ $selectedDay }}
                </div>
                <div class="text-xs text-slate-500 font-semibold">
                    {{ $classes->count() }} Classes
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <!-- Column Headers (Classrooms & Divisions) -->
                    <thead>
                        <tr class="bg-slate-100/80 dark:bg-[#080c14] border-b border-slate-200 dark:border-white/10">
                            <th class="p-3 text-left font-bold uppercase tracking-wider text-slate-500 border-r border-slate-200 dark:border-white/10 sticky left-0 bg-slate-100 dark:bg-[#080c14] z-10 w-28 min-w-[110px]">
                                Time / Class
                            </th>
                            @foreach ($classes as $class)
                                <th class="p-3 text-center border-r border-slate-200 dark:border-white/10 min-w-[130px] max-w-[170px]">
                                    <div class="font-extrabold text-slate-900 dark:text-white text-xs">
                                        {{ $class->year }} {{ $class->name }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <!-- Time Slot Rows -->
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @foreach ($timeSlots as $slot)
                            @php
                                $isLunch = str_contains($slot, '12:30') || str_contains($slot, '13:00');
                            @endphp

                            @if ($isLunch)
                                <tr class="bg-amber-500/5 dark:bg-yellow-500/10 border-y border-amber-500/20 text-center">
                                    <td class="p-3 font-mono font-bold text-amber-700 dark:text-yellow-400 border-r border-amber-500/20 sticky left-0 bg-amber-50 dark:bg-[#151206] z-10 whitespace-nowrap text-left">
                                        {{ str_replace('-', '–', $slot) }}
                                    </td>
                                    <td colspan="{{ $classes->count() }}" class="p-3 font-extrabold tracking-widest text-amber-700 dark:text-yellow-400 uppercase text-xs">
                                        B R E A K &nbsp; / &nbsp; L U N C H
                                    </td>
                                </tr>
                                @continue
                            @endif

                            <tr class="hover:bg-slate-50/40 dark:hover:bg-white/[0.01]">
                                <!-- Time Label Column -->
                                <td class="p-3 border-r border-slate-200 dark:border-white/10 font-mono font-semibold text-slate-500 text-xs text-left sticky left-0 bg-white dark:bg-[#0f172a] z-10 whitespace-nowrap">
                                    {{ str_replace('-', '–', $slot) }}
                                </td>

                                <!-- Class Cells -->
                                @foreach ($classes as $class)
                                    @php
                                        $lec = $grid[$slot][$class->id] ?? null;
                                    @endphp

                                    <td class="p-2 border-r border-slate-200 dark:border-white/10 align-top">
                                        @if ($lec)
                                            @if ($lec->subject === 'BREAK')
                                                <div class="text-center py-2 font-bold text-[11px] text-amber-600 dark:text-yellow-400">
                                                    BREAK
                                                </div>
                                            @else
                                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-black/30 border border-slate-200/80 dark:border-white/5 space-y-1 hover:border-blue-400 transition">
                                                    <p class="font-bold text-slate-900 dark:text-white leading-tight line-clamp-2" title="{{ $lec->subject }}">
                                                        {{ $lec->subject }}
                                                    </p>
                                                    <p class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold truncate" title="{{ $lec->faculty?->name }}">
                                                        {{ $lec->faculty?->name ?? '—' }}
                                                    </p>
                                                    @if ($lec->room && $lec->room !== '-')
                                                        <div class="pt-0.5">
                                                            <span class="inline-block px-1.5 py-0.5 rounded bg-slate-200/60 dark:bg-white/10 text-[10px] font-mono font-bold text-slate-600 dark:text-slate-300">
                                                                Rm {{ $lec->room }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-3 text-slate-300 dark:text-slate-600 text-xs">
                                                —
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    @endif

</div>
