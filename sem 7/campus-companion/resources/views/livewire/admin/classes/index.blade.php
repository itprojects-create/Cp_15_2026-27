<?php

use App\Models\Department;
use App\Models\ClassRoom;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {

    public $departments;
    public $selectedDepartment = '';
    public $selectedYear = '';
    public $selectedClass = null;

    public $students = [];

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedDepartment', 'selectedYear'])) {
            $this->selectedClass = null;
            $this->students = [];
        }
    }

    public function selectClass($id)
    {
        $this->selectedClass = $id;
        $this->students = User::where('class_room_id', $id)->get();
    }

    public function deleteClass($id)
    {
        ClassRoom::destroy($id);
        if ($this->selectedClass == $id) {
            $this->selectedClass = null;
            $this->students = [];
        }
        session()->flash('success', 'Class deleted successfully.');
    }

    public function getClassesProperty()
    {
        return ClassRoom::with('department')
            ->withCount('students')
            ->when($this->selectedDepartment, fn($q) => $q->where('department_id', $this->selectedDepartment))
            ->when($this->selectedYear, fn($q) => $q->where('year', $this->selectedYear))
            ->get();
    }
};
?>

<div class="space-y-8 pb-12">

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:via-[#0b1120] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-8 shadow-sm dark:shadow-xl">
        <div class="absolute inset-0 opacity-10 dark:opacity-20" style="background-image:linear-gradient(currentColor 1px,transparent 1px),linear-gradient(90deg,currentColor 1px,transparent 1px);background-size:40px 40px; color: rgba(100,116,139,0.3);"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-semibold mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 dark:bg-indigo-400 animate-pulse"></span>
                    Classroom Directory
                </div>
                <h1 class="font-display font-extrabold text-3xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    Classes & Cohorts
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Browse classroom divisions, academic years, and enrolled students.</p>
            </div>

            <a href="/admin/classes/create" wire:navigate
               class="inline-flex items-center gap-2 bg-[#d2e823] text-black font-bold text-xs px-5 py-3 rounded-xl hover:opacity-90 active:scale-[0.98] transition shadow-lg shadow-[#d2e823]/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Add Class
            </a>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="flex flex-col sm:flex-row items-center gap-4">
        <div class="w-full sm:w-1/2">
            <select wire:model.live="selectedDepartment"
                class="w-full p-3 rounded-xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white text-sm outline-none focus:border-[#d2e823]/40 shadow-sm dark:shadow-none transition">
                <option value="">All Departments</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full sm:w-1/2">
            <select wire:model.live="selectedYear"
                class="w-full p-3 rounded-xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white text-sm outline-none focus:border-[#d2e823]/40 shadow-sm dark:shadow-none transition">
                <option value="">All Academic Years (FY, SY, TY, Final Year)</option>
                <option value="FY">First Year (FY)</option>
                <option value="SY">Second Year (SY)</option>
                <option value="TY">Third Year (TY)</option>
                <option value="Final Year">Final Year</option>
            </select>
        </div>
    </div>

    <!-- CLASSES GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($this->classes as $class)
            <div wire:click="selectClass({{ $class->id }})" wire:key="class-{{ $class->id }}"
                class="cursor-pointer group relative p-6 rounded-3xl border transition-all duration-200 shadow-sm dark:shadow-none flex flex-col justify-between
                {{ $selectedClass === $class->id ? 'bg-white dark:bg-[#0f172a] border-lime-500 dark:border-[#d2e823] ring-2 ring-lime-500/20 dark:ring-[#d2e823]/30 shadow-md' : 'bg-white dark:bg-[#0f172a]/70 border-slate-200 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/15' }}">

                <div>
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20">
                            {{ $class->year }} Division
                        </span>

                        <div class="flex items-center gap-2">
                            <a href="/admin/classes/edit/{{ $class->id }}" wire:navigate @click.stop
                               class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white transition font-semibold">
                                Edit
                            </a>
                            <button wire:click.stop="deleteClass({{ $class->id }})"
                                    wire:confirm="Are you sure you want to delete this class division?"
                                    class="text-xs px-2.5 py-1 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 hover:text-red-700 transition font-semibold">
                                Delete
                            </button>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-lime-600 dark:group-hover:text-[#d2e823] transition-colors">
                        {{ $class->year }} - {{ $class->name }}
                    </h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                        {{ $class->department->name }}
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-semibold flex items-center gap-1 text-slate-600 dark:text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M16 14a4 4 0 10-8 0M12 14v7M5 21h14" />
                        </svg>
                        {{ $class->students_count }} Students
                    </span>
                    <span class="text-lime-600 dark:text-[#d2e823] font-bold flex items-center gap-1">{{ $selectedClass === $class->id ? 'Viewing Roster' : 'View Roster' }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white dark:bg-[#0f172a]/50 border border-slate-200 dark:border-white/5 rounded-3xl p-8 shadow-sm dark:shadow-none">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3 text-slate-400 dark:text-slate-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">No classes match criteria</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Select a different filter or add new classes.</p>
            </div>
        @endforelse
    </div>

    <!-- STUDENTS ROSTER -->
    @if($selectedClass)
        <div class="pt-6 border-t border-slate-200 dark:border-white/5 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Class Student Roster</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ count($students) }} students enrolled in this division</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($students as $student)
                    <div wire:key="student-{{ $student->id }}" class="flex items-center gap-3.5 p-4 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/5 hover:border-slate-300 dark:hover:border-white/15 transition shadow-sm dark:shadow-none">
                        <x-user-avatar :user="$student" size="md" />
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate leading-snug">
                                {{ $student->name }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                {{ $student->email }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center text-slate-400 dark:text-slate-500 text-sm bg-white dark:bg-[#0f172a]/30 border border-slate-200 dark:border-white/5 rounded-2xl">
                        No students enrolled in this class yet.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
