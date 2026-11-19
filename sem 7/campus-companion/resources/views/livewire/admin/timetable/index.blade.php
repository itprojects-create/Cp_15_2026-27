<?php

use App\Models\Department;
use App\Models\ClassRoom;
use App\Models\Timetable;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    public $departments;
    public $selectedDepartment = null;
    public $classes = [];
    public $selectedClass = null;
    public $timetable = [];

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function selectDepartment($id)
    {
        $this->selectedDepartment = $id;
        $this->selectedClass = null;
        $this->timetable = [];

        $this->classes = ClassRoom::where('department_id', $id)->get();
    }

    public function selectClass($id)
    {
        $this->selectedClass = $id;

        $data = Timetable::with('faculty')
            ->where('class_room_id', $id)
            ->get();

        $slots = ['08:30-09:30', '09:30-10:30', '10:30-11:30', '11:30-12:30', '12:30-01:30', '01:30-02:30', '02:30-03:30'];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $timetable = [];

        // create full grid (important)
        foreach ($slots as $slot) {
            foreach ($days as $day) {
                $timetable[$slot][$day] = null;
            }
        }

        // fill data
        foreach ($data as $item) {
            $normalizedSlot = Timetable::normalizeSlot($item->time_slot);
            $slotKey = isset($timetable[$item->time_slot]) ? $item->time_slot : (isset($timetable[$normalizedSlot]) ? $normalizedSlot : $item->time_slot);
            $timetable[$slotKey][$item->day] = $item;
        }

        $this->timetable = $timetable;
    }

    public function deleteLecture($id)
    {
        Timetable::destroy($id);
        $this->selectClass($this->selectedClass);
    }

    public function deleteClassTimetable()
    {
        Timetable::where('class_room_id', $this->selectedClass)->delete();
        $this->selectClass($this->selectedClass);
    }

    // Clash Reporting State & Methods
    public bool $showClashModal = false;
    public string $clashDay = '';
    public string $clashTimeSlot = '';
    public string $clashingClassId = '';
    public array $clashAlternatives = [];
    public bool $clashSearched = false;

    public function openClashModal()
    {
        $this->showClashModal = true;
        $this->clashSearched = false;
        $this->clashAlternatives = [];
        $this->clashDay = 'Monday';
        $this->clashTimeSlot = '08:30-09:30';
        $this->clashingClassId = $this->selectedClass ? (string) $this->selectedClass : '';
    }

    public function closeClashModal()
    {
        $this->showClashModal = false;
    }

    public function findClashAlternatives()
    {
        $this->validate([
            'clashDay' => 'required',
            'clashTimeSlot' => 'required',
            'clashingClassId' => 'required|exists:class_rooms,id',
        ]);

        $clashingClass = ClassRoom::find($this->clashingClassId);

        $normalizedSlot = Timetable::normalizeSlot($this->clashTimeSlot);

        $busyClassroomIds = Timetable::where('day', $this->clashDay)
            ->where(function ($q) use ($normalizedSlot) {
                $q->where('time_slot', $this->clashTimeSlot)
                  ->orWhere('time_slot', $normalizedSlot);
            })
            ->pluck('class_room_id')
            ->toArray();

        $excludeIds = array_filter(array_unique(array_merge($busyClassroomIds, [(int) $this->clashingClassId])));

        $available = ClassRoom::with(['department'])
            ->leftJoin('floor_blocks', 'class_rooms.id', '=', 'floor_blocks.class_room_id')
            ->leftJoin('floors', 'floor_blocks.floor_id', '=', 'floors.id')
            ->select('class_rooms.*', 'floors.name as floor_name', 'floor_blocks.block_number')
            ->whereNotIn('class_rooms.id', $excludeIds)
            ->get()
            ->unique('id');

        $this->clashAlternatives = $available->map(function ($room) use ($clashingClass) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'year' => $room->year,
                'department_id' => $room->department_id,
                'department_name' => $room->department?->name,
                'floor_name' => $room->floor_name,
                'block_number' => $room->block_number,
                'is_same_dept' => $clashingClass && $room->department_id === $clashingClass->department_id,
            ];
        })->sortByDesc('is_same_dept')->values()->toArray();

        $this->clashSearched = true;
    }
};
?>

<div class="space-y-10">

    <!-- Header -->
    <div class="flex justify-between items-start sm:items-center flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Timetable</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">View and manage departmental class schedules</p>
        </div>
        <div class="flex items-center flex-wrap gap-2.5">
            <a href="/admin/timetable/master" wire:navigate class="bg-[#d2e823] hover:opacity-90 active:scale-[0.98] px-4 py-2 rounded-xl text-black text-xs font-bold flex items-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                Master Grid
            </a>
            <a href="/admin/timetable/import" wire:navigate class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-800 dark:text-slate-200 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Import Excel
            </a>
            <a href="/admin/timetable/create" wire:navigate class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-800 dark:text-slate-200 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Lecture
            </a>
            <button type="button" wire:click="openClashModal" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-xl text-white text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-blue-600/20 active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Report Clash
            </button>
        </div>
    </div>

    @include('components.report-clash-modal')

    <!-- STEP 1: DEPARTMENTS -->
    <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">1. Select Department</h2>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($departments as $dept)
                <div wire:click="selectDepartment({{ $dept->id }})"
                    class="cursor-pointer p-5 rounded-2xl transition-all duration-200 shadow-sm dark:shadow-none
                    {{ $selectedDepartment == $dept->id ? 'bg-[#d2e823] text-black ring-2 ring-[#d2e823]' : 'bg-white dark:bg-[#0b1120] border border-slate-200 dark:border-white/5 hover:border-blue-500 text-slate-900 dark:text-white hover:shadow-md' }}">
                    <div class="font-bold text-base leading-snug">
                        {{ $dept->name }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- STEP 2: CLASSES -->
    @if ($selectedDepartment)
        <div class="space-y-3 pt-2">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">2. Select Class</h2>

            <div class="grid sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($classes as $class)
                    <div wire:click="selectClass({{ $class->id }})"
                        class="cursor-pointer p-4 rounded-xl text-center font-semibold text-sm transition-all duration-200 shadow-sm dark:shadow-none
                        {{ $selectedClass == $class->id ? 'bg-blue-600 text-white ring-2 ring-blue-500 shadow-md' : 'bg-white dark:bg-[#020617] border border-slate-200 dark:border-white/5 text-slate-800 dark:text-slate-200 hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-white/[0.03]' }}">
                        {{ $class->year }} - {{ $class->name }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- STEP 3: TIMETABLE -->
    @if ($selectedClass && count($timetable))
        <div class="space-y-4 pt-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Timetable Grid</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="/admin/timetable/edit?class_room_id={{ $selectedClass }}"
                       class="bg-[#d2e823] hover:opacity-90 active:scale-[0.98] px-4 py-2 rounded-xl text-black text-xs font-bold transition shadow-sm">
                        Edit Timetable
                    </a>
                    <button wire:click="deleteClassTimetable"
                            wire:confirm="Are you sure you want to delete the entire timetable for this class?"
                            class="bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-xs font-bold transition">
                        Delete Class Timetable
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm dark:shadow-none">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50 dark:bg-[#0b1120] text-slate-700 dark:text-slate-300 font-semibold border-b border-slate-200 dark:border-white/10">
                        <tr>
                            <th class="p-3.5 border-r border-slate-200 dark:border-white/10 text-left text-xs uppercase tracking-wider w-28">Time</th>
                            <th class="p-3.5 border-r border-slate-200 dark:border-white/10 text-center text-xs uppercase tracking-wider">Monday</th>
                            <th class="p-3.5 border-r border-slate-200 dark:border-white/10 text-center text-xs uppercase tracking-wider">Tuesday</th>
                            <th class="p-3.5 border-r border-slate-200 dark:border-white/10 text-center text-xs uppercase tracking-wider">Wednesday</th>
                            <th class="p-3.5 border-r border-slate-200 dark:border-white/10 text-center text-xs uppercase tracking-wider">Thursday</th>
                            <th class="p-3.5 text-center text-xs uppercase tracking-wider">Friday</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        @endphp

                        @foreach ($timetable as $slot => $daysData)
                            <tr class="text-center">
                                <!-- TIME -->
                                <td class="p-3 border-r border-slate-200 dark:border-white/10 text-slate-500 font-mono text-xs text-left whitespace-nowrap">
                                    {{ str_replace('-', '–', $slot) }}
                                </td>

                                @foreach ($days as $dayIndex => $day)
                                    @php
                                        $item = $daysData[$day] ?? null;
                                        $isLastCol = $dayIndex === count($days) - 1;
                                    @endphp

                                    <td class="p-2.5 {{ !$isLastCol ? 'border-r border-slate-200 dark:border-white/10' : '' }}">
                                        @if ($item)
                                            @if ($item->subject === 'BREAK')
                                                <span class="text-amber-600 dark:text-yellow-400 font-bold text-xs">
                                                    BREAK
                                                </span>
                                            @else
                                                <div class="bg-slate-50 dark:bg-[#080c14] border border-slate-200/80 dark:border-white/5 rounded-xl p-2.5 shadow-xs">
                                                    <div class="text-slate-900 dark:text-white font-bold leading-snug">
                                                        {{ $item->subject }}
                                                    </div>

                                                    <div class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-0.5">
                                                        {{ $item->faculty?->name ?? '—' }}
                                                    </div>

                                                    <div class="text-[0.65rem] text-slate-500 mt-0.5 font-medium">
                                                        Room {{ $item->room }}
                                                    </div>

                                                    <div class="mt-2 flex justify-center gap-2 text-[11px] pt-1 border-t border-slate-200/60 dark:border-white/5">
                                                        <a href="/admin/timetable/edit/{{ $item->id }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">Edit</a>
                                                        <button wire:click="deleteLecture({{ $item->id }})" wire:confirm="Delete this lecture?" class="text-red-500 dark:text-red-400 hover:underline font-semibold">Delete</button>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
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
