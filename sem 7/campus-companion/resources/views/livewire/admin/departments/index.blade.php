<?php

use App\Models\Department;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {

    public $search = '';

    public function delete($id)
    {
        Department::find($id)?->delete();
        session()->flash('success', 'Department removed successfully');
    }

    public function getDepartmentsProperty()
    {
        return Department::withCount(['users', 'classRooms'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->get();
    }
};
?>

<div class="space-y-8 pb-12">

    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 p-8 shadow-sm dark:shadow-xl transition-colors">
        <div class="absolute inset-0 opacity-5 dark:opacity-20" style="background-image:linear-gradient(rgba(0,0,0,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,0.04) 1px,transparent 1px);background-size:40px 40px; dark:background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-semibold mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse"></span>
                    Academic Organization
                </div>
                <h1 class="font-display font-extrabold text-3xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    Academic Departments
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Configure and manage branches, faculty allocations, and classrooms.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="/admin/departments/create" wire:navigate
                   class="inline-flex items-center gap-2 bg-[#d2e823] text-black font-bold text-xs px-5 py-3 rounded-xl hover:opacity-90 active:scale-[0.98] transition shadow-lg shadow-[#d2e823]/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Add Department
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-2xl text-sm font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- SEARCH & STATS BAR -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
            <input wire:model.live.debounce.300ms="search" placeholder="Search departments..."
                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-[#d2e823]/40 focus:ring-1 focus:ring-[#d2e823]/20 transition" />
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
            <span>{{ count($this->departments) }} Active Departments</span>
        </div>
    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        @forelse ($this->departments as $dept)

            <div class="group relative bg-white dark:bg-[#0f172a]/80 border border-slate-200/80 dark:border-white/5 rounded-3xl p-6 hover:bg-slate-50/50 dark:hover:bg-[#0f172a] hover:border-blue-500/30 dark:hover:border-blue-500/30 transition-all duration-300 shadow-sm dark:shadow-lg flex flex-col justify-between">

                <div>
                    <!-- Card Top -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M3 21h18M4 21V7l8-4 8 4v14" />
                            </svg>
                        </div>

                        <span class="text-[0.65rem] font-bold px-2.5 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full uppercase tracking-wider">
                            Active
                        </span>
                    </div>

                    <!-- Title -->
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $dept->name }}
                    </h2>

                    <!-- Meta details -->
                    <div class="grid grid-cols-2 gap-2 mt-5 p-3 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-100 dark:border-white/[0.03]">
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Members</p>
                            <p class="text-base font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $dept->users_count }}</p>
                        </div>
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Classrooms</p>
                            <p class="text-base font-extrabold text-lime-600 dark:text-[#d2e823] mt-0.5">{{ $dept->class_rooms_count }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer & Actions -->
                <div class="flex items-center justify-between pt-5 mt-5 border-t border-slate-100 dark:border-white/5 text-xs">
                    <span class="text-slate-400 dark:text-slate-500 text-[0.7rem]">
                        Added {{ $dept->created_at->format('d M Y') }}
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="/admin/departments/edit/{{ $dept->id }}" wire:navigate
                           class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition font-medium">
                            Edit
                        </a>

                        <button wire:click="delete({{ $dept->id }})"
                                wire:confirm="Are you sure you want to delete the {{ $dept->name }} department? This action cannot be undone."
                                class="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition font-medium">
                            Delete
                        </button>
                    </div>
                </div>

            </div>

        @empty

            <div class="col-span-full py-16 text-center bg-white dark:bg-[#0f172a]/50 border border-slate-200 dark:border-white/5 rounded-3xl p-8">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-3 text-slate-400 dark:text-slate-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M3 21h18M4 21V7l8-4 8 4v14" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">No departments found</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Try a different search query or add a new department.</p>
            </div>

        @endforelse

    </div>

</div>

