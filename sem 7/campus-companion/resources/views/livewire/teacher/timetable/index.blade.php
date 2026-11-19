<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Timetable;
use App\Models\ClassRoom;

new #[Layout('components.layouts.teacher')] class extends Component {

    public $timetable = [];

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
        $this->clashDay = '';
        $this->clashTimeSlot = '';
        $this->clashingClassId = '';
        $this->clashAlternatives = [];
        $this->clashSearched = false;
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

    public function mount()
    {
        $user = auth()->user();

        if (!$user) return;

        // Eager load faculty and classRoom
        $data = Timetable::with(['faculty', 'classRoom'])
            ->where('faculty_id', $user->id)
            ->get();

        $slots = [
            '08:30-09:30',
            '09:30-10:30',
            '10:30-11:30',
            '11:30-12:30',
            '12:30-01:30',
            '01:30-02:30',
            '02:30-03:30',
        ];

        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

        $grouped = [];

        // initialize empty grid
        foreach ($slots as $slot) {
            foreach ($days as $day) {
                $grouped[$slot][$day] = null;
            }
        }

        // fill data
        foreach ($data as $row) {
            $normalizedSlot = Timetable::normalizeSlot($row->time_slot);
            $slotKey = isset($grouped[$row->time_slot]) ? $row->time_slot : (isset($grouped[$normalizedSlot]) ? $normalizedSlot : $row->time_slot);
            $grouped[$slotKey][$row->day] = [
                'subject' => $row->subject,
                'room' => $row->room,
                'class' => $row->classRoom ? ($row->classRoom->year . ' ' . $row->classRoom->name) : null,
                'faculty' => $row->faculty?->name,
            ];
        }

        $this->timetable = $grouped;
    }
};
?>

<div class="space-y-10">

    <div class="flex justify-between items-start sm:items-center flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">My Schedule</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                Your weekly teaching timetable
            </p>
        </div>
        <button type="button" wire:click="openClashModal" class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-xl text-white text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-blue-600/20 active:scale-[0.98]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Report Clash
        </button>
    </div>

    @include('components.report-clash-modal')

    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl overflow-x-auto shadow-sm dark:shadow-none">

        <table class="w-full text-sm text-center">

            <thead class="bg-slate-50 dark:bg-[#080c14] text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-white/5">
                <tr>
                    <th class="p-3.5 text-left text-xs uppercase tracking-wider">Time</th>
                    <th class="p-3.5 text-xs uppercase tracking-wider">Mon</th>
                    <th class="p-3.5 text-xs uppercase tracking-wider">Tue</th>
                    <th class="p-3.5 text-xs uppercase tracking-wider">Wed</th>
                    <th class="p-3.5 text-xs uppercase tracking-wider">Thu</th>
                    <th class="p-3.5 text-xs uppercase tracking-wider">Fri</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 dark:divide-white/5">

                @foreach($this->timetable as $time => $days)

                    @if($time === '12:30-01:30')
                        <tr class="bg-amber-500/5 dark:bg-yellow-500/10 border-y border-amber-500/15 dark:border-yellow-500/15">
                            <td colspan="6" class="p-3.5 font-bold text-amber-700 dark:text-yellow-400 text-xs tracking-wider uppercase">
                                Lunch Break — 12:30 – 01:30
                            </td>
                        </tr>
                        @continue
                    @endif

                    <tr>

                        <td class="p-3.5 text-slate-500 font-mono text-xs text-left whitespace-nowrap">
                            {{ str_replace('-', '–', $time) }}
                        </td>

                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day)

                            @php
                                $cell = $days[$day] ?? null;
                            @endphp

                            <td class="p-2.5">

                                @if($cell)

                                    <div class="bg-slate-50 dark:bg-[#080c14] border border-slate-200/80 dark:border-white/5 rounded-xl p-3 hover:bg-slate-100 dark:hover:bg-[#0c1324] transition shadow-xs text-center">

                                        <p class="font-bold text-slate-900 dark:text-white leading-snug">
                                            {{ $cell['subject'] }}
                                        </p>

                                        @if(!empty($cell['class']))
                                            <p class="text-xs text-lime-600 dark:text-[#d2e823] font-semibold mt-0.5">
                                                {{ $cell['class'] }}
                                            </p>
                                        @endif

                                        <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-0.5">
                                            Room {{ $cell['room'] }}
                                        </p>

                                    </div>

                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif

                            </td>

                        @endforeach

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
