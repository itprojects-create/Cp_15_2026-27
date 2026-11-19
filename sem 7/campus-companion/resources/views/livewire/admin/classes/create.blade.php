<?php

use App\Models\ClassRoom;
use App\Models\Department;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    public $year = 'FY';
    public $name = '';
    public $department_id = '';
    public $departments = [];

    public function mount()
    {
        $this->departments = Department::orderBy('name')->get();
        if ($this->departments->isNotEmpty()) {
            $this->department_id = $this->departments->first()->id;
        }
    }

    public function save()
    {
        $this->validate([
            'year' => 'required|string|max:20',
            'name' => 'required|string|max:50',
            'department_id' => 'required|exists:departments,id',
        ]);

        ClassRoom::create([
            'year' => $this->year,
            'name' => $this->name,
            'department_id' => $this->department_id,
        ]);

        session()->flash('success', 'Class created successfully.');

        return redirect('/admin/classes');
    }
};
?>

<div class="max-w-xl mx-auto space-y-6 pb-12">
    <div>
        <a href="/admin/classes" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white mb-3 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to Classes
        </a>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Add New Class</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Create a new classroom division or academic cohort.</p>
    </div>

    <form wire:submit="save" class="bg-white dark:bg-[#0b1120] border border-slate-200 dark:border-white/5 p-6 rounded-3xl space-y-5 shadow-sm dark:shadow-xl">
        <!-- Academic Year -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Academic Year</label>
            <select wire:model="year"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="FY">First Year (FY)</option>
                <option value="SY">Second Year (SY)</option>
                <option value="TY">Third Year (TY)</option>
                <option value="Final Year">Final Year (BE/B.Tech)</option>
            </select>
            @error('year')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Division Name -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Division / Class Name</label>
            <input type="text" wire:model="name" placeholder="e.g. Division A, D10, Section 1"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Department -->
        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Department</label>
            <select wire:model="department_id"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="">Select Department</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            @error('department_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/5">
            <a href="/admin/classes"
                class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                Cancel
            </a>

            <button type="submit"
                class="px-6 py-2.5 bg-[#d2e823] text-black font-bold rounded-xl hover:opacity-90 active:scale-[0.98] transition shadow-lg shadow-[#d2e823]/10 text-sm">
                Create Class
            </button>
        </div>
    </form>
</div>
