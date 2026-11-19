<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use App\Models\Department;

return new #[Layout('components.layouts.admin')] class extends Component {

    public $departmentFilter = '';
    public $departments = [];

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function delete($id)
    {
        Event::findOrFail($id)->delete();
        session()->flash('success', 'Event deleted successfully');
    }

    public function getEventsProperty()
    {
        return Event::with('department')
            ->when($this->departmentFilter, function ($q) {
                $q->where('department_id', $this->departmentFilter);
            })
            ->orderBy('date')
            ->get();
    }
};
?>

<div class="space-y-8 pb-12">

    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0f172a] via-[#0b1120] to-[#080c14] border border-white/5 p-8 shadow-xl">
        <div class="absolute inset-0 opacity-20" style="background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:40px 40px;"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Campus Activities
                </div>
                <h1 class="font-display font-extrabold text-3xl text-white tracking-tight leading-tight">
                    Campus Events
                </h1>
                <p class="text-slate-400 text-sm mt-1">Plan, publish, and manage college events and workshops.</p>
            </div>

            <a href="/admin/events/create"
               class="inline-flex items-center gap-2 bg-[#d2e823] text-black font-bold text-xs px-5 py-3 rounded-xl hover:opacity-90 active:scale-[0.98] transition shadow-lg shadow-[#d2e823]/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New Event
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-2xl text-sm font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Events</p>
            <p class="font-display font-extrabold text-2xl text-slate-900 dark:text-white mt-1">{{ $this->events->count() }}</p>
        </div>

        <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Upcoming</p>
            <p class="font-display font-extrabold text-2xl text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $this->events->where('date','>=',now()->toDateString())->count() }}
            </p>
        </div>

        <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Past Events</p>
            <p class="font-display font-extrabold text-2xl text-slate-600 dark:text-slate-400 mt-1">
                {{ $this->events->where('date','<',now()->toDateString())->count() }}
            </p>
        </div>
    </div>

    <!-- FILTER -->
    <div class="flex items-center gap-3">
        <select wire:model.live="departmentFilter"
            class="px-4 py-2.5 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 shadow-sm dark:shadow-none transition">
            <option value="">All Departments (Global & Departmental)</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- EVENTS LIST -->
    <div class="space-y-4">
        @forelse($this->events as $e)
            <div class="group relative bg-white dark:bg-[#0f172a]/80 border border-slate-200 dark:border-white/5 rounded-2xl p-6 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-amber-500/30 transition-all duration-200 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm dark:shadow-md">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @if($e->department)
                            <span class="text-[0.65rem] font-bold px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 uppercase tracking-wider">
                                {{ $e->department->name }}
                            </span>
                        @else
                            <span class="text-[0.65rem] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-wider">
                                Global Campus
                            </span>
                        @endif

                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                            {{ \Carbon\Carbon::parse($e->date)->format('D, d M Y') }}
                        </span>
                    </div>

                    <h2 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-lime-600 dark:group-hover:text-[#d2e823] transition-colors leading-snug">
                        {{ $e->title }}
                    </h2>

                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed line-clamp-2">
                        {{ $e->description }}
                    </p>

                    <div class="flex items-center gap-2 mt-3 text-xs text-slate-500 font-medium">
                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span>{{ $e->location }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                    <a href="/admin/events/edit/{{ $e->id }}"
                        class="px-4 py-2 text-xs font-semibold bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl transition">
                        Edit
                    </a>

                    <button wire:click="delete({{ $e->id }})"
                        wire:confirm="Are you sure you want to delete this event?"
                        class="px-4 py-2 text-xs font-semibold bg-red-500/10 hover:bg-red-500/20 text-red-500 dark:text-red-400 rounded-xl transition">
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="py-16 text-center bg-white dark:bg-[#0f172a]/50 border border-slate-200 dark:border-white/5 rounded-3xl p-8 shadow-sm dark:shadow-none">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3 text-slate-400 dark:text-slate-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">No events scheduled</h3>
                <p class="text-slate-500 text-xs mt-1">Create an event to broadcast to students and faculty.</p>
            </div>
        @endforelse
    </div>

</div>

