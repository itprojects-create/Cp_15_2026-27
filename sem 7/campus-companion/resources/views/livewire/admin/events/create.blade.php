<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use App\Models\Department;

return new #[Layout('components.layouts.admin')] class extends Component {
    public $title = '';
    public $description = '';
    public $date = '';
    public $location = '';
    public $department_id = '';

    public $departments = [];

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'date' => 'required|date',
            'location' => 'required',
        ]);

        Event::create([
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'department_id' => $this->department_id ?: null,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Event created successfully');

        return redirect('/admin/events');
    }
};
?>

<div class="space-y-8 max-w-2xl">

    <div>
        <a href="/admin/events" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white mb-3 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to Events
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Create Event</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Fill in the details below to add a new campus event.</p>
    </div>

    <!-- Live Preview -->
    <div class="flex items-center gap-3 p-4 rounded-2xl bg-lime-500/10 border border-lime-500/20">
        <div class="w-10 h-10 rounded-xl bg-lime-500/20 flex items-center justify-center text-lime-700 dark:text-[#d2e823] font-bold">
            E
        </div>
        <div>
            <p class="text-slate-900 dark:text-[#d2e823] text-sm font-semibold">
                {{ $title ?: 'Event Title Preview' }}
            </p>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                {{ $date ? \Carbon\Carbon::parse($date)->format('d M Y') : 'Date TBD' }}
                {{ $location ? ' · ' . $location : '' }}
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 p-6 rounded-3xl space-y-5 shadow-sm dark:shadow-none">

        <!-- Title -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Event Title</label>
            <input wire:model.live="title" placeholder="e.g. Annual Tech Symposium"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition" />
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Description</label>
            <textarea wire:model="description" rows="4" placeholder="Full event itinerary, key speakers, requirements..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition resize-none"></textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Date + Location -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Date</label>
                <input type="date" wire:model.live="date"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition" />
                @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Location / Venue</label>
                <input wire:model.live="location" placeholder="e.g. Main Auditorium"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition" />
                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Department Scope -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Department Scope</label>
            <select wire:model="department_id"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="">All Departments (Global Campus Event)</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="pt-4 flex gap-3">
            <button wire:click="save"
                wire:loading.attr="disabled"
                class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] disabled:opacity-50 inline-flex items-center gap-2 transition shadow-md shadow-[#d2e823]/10">
                <span wire:loading.remove wire:target="save">Create Event</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                </span>
            </button>

            <a href="/admin/events"
                class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                Cancel
            </a>
        </div>

    </div>

</div>
