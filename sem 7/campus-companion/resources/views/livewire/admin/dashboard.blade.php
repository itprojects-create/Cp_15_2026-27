<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Complaint;
use App\Models\Notice;
use App\Models\Event;

return new #[Layout('components.layouts.admin')] class extends Component {

    public function getStatsProperty()
    {
        return [
            'users'      => User::count(),
            'complaints' => Complaint::count(),
            'pending'    => Complaint::where('status', 'pending')->count(),
            'notices'    => Notice::count(),
            'events'     => Event::count(),
        ];
    }
};
?>

<div>

    {{-- ── PAGE HEADER ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 p-6 sm:p-8 shadow-sm dark:shadow-xl mb-8 transition-colors">
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-[#d2e823]/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <x-user-avatar size="lg" class="shadow-lg ring-2 ring-[#d2e823]/40 shrink-0" />
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[0.7rem] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400">Admin</span>
                        <span class="text-slate-400 dark:text-slate-600 text-xs">›</span>
                        <span class="text-[0.7rem] font-bold tracking-wider uppercase text-lime-600 dark:text-[#d2e823]">Dashboard</span>
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        Welcome, <span class="text-lime-600 dark:text-[#d2e823]">{{ auth()->user()->name }}</span>
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1">
                        Full campus management system overview.
                    </p>
                </div>
            </div>
            <a href="/admin/users/create" wire:navigate
               class="inline-flex items-center gap-2 bg-[#d2e823] text-black font-bold text-xs px-5 py-3 rounded-full hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10 shrink-0 self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Create User
            </a>
        </div>
    </div>

    {{-- ── STATS GRID ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Users --}}
        <div class="bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-all group">
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <p class="text-[0.7rem] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400 mb-2">Total Users</p>
            <p class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100 leading-none">{{ $this->stats['users'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-medium">All roles</p>
        </div>

        {{-- Complaints --}}
        <div class="bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-all group">
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-red-500/10 dark:bg-red-500/15 border border-red-500/20 flex items-center justify-center text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <p class="text-[0.7rem] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400 mb-2">Complaints</p>
            <p class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100 leading-none">{{ $this->stats['complaints'] }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block animate-pulse"></span>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $this->stats['pending'] }} pending</span>
            </div>
        </div>

        {{-- Notices --}}
        <div class="bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-all group">
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-lime-500/10 dark:bg-[#d2e823]/15 border border-lime-500/20 dark:border-[#d2e823]/20 flex items-center justify-center text-lime-600 dark:text-[#d2e823] group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-[0.7rem] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400 mb-2">Notices</p>
            <p class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100 leading-none">{{ $this->stats['notices'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-medium">Published</p>
        </div>

        {{-- Events --}}
        <div class="bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-all group">
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-blue-500/10 dark:bg-blue-500/15 border border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <p class="text-[0.7rem] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400 mb-2">Events</p>
            <p class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100 leading-none">{{ $this->stats['events'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-medium">Active</p>
        </div>

    </div>

    {{-- ── QUICK ACTIONS ── --}}
    <div class="mb-4">
        <p class="text-[0.7rem] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400">Quick Actions</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <a href="/admin/notices" wire:navigate
           class="flex items-center gap-4 bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-lime-500/40 dark:hover:border-[#d2e823]/30 dark:hover:bg-[#0f1929] transition-all group">
            <div class="w-10 h-10 rounded-xl bg-lime-500/10 dark:bg-[#d2e823]/10 border border-lime-500/20 dark:border-[#d2e823]/20 flex items-center justify-center shrink-0 text-lime-600 dark:text-[#d2e823] group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-display font-bold text-sm text-slate-900 dark:text-slate-100 group-hover:text-lime-600 dark:group-hover:text-[#d2e823] transition-colors truncate">Manage Notices</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">Create, edit & publish</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-400 dark:text-slate-600 group-hover:text-lime-600 dark:group-hover:text-[#d2e823] group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="/admin/events" wire:navigate
           class="flex items-center gap-4 bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-blue-500/40 dark:hover:border-blue-500/30 dark:hover:bg-[#0f1929] transition-all group">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-500/15 border border-blue-500/20 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-display font-bold text-sm text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">Manage Events</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">Add & schedule events</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-400 dark:text-slate-600 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="/admin/users" wire:navigate
           class="flex items-center gap-4 bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-indigo-500/40 dark:hover:border-indigo-500/30 dark:hover:bg-[#0f1929] transition-all group">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/15 border border-indigo-500/20 flex items-center justify-center shrink-0 text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-display font-bold text-sm text-slate-900 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">Manage Users</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">Roles & permissions</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-400 dark:text-slate-600 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>

        <a href="/admin/complaints" wire:navigate
           class="flex items-center gap-4 bg-white dark:bg-[#0b1120] border border-slate-200/80 dark:border-white/5 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-red-500/40 dark:hover:border-red-500/30 dark:hover:bg-[#0f1929] transition-all group">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 dark:bg-red-500/15 border border-red-500/20 flex items-center justify-center shrink-0 text-red-600 dark:text-red-400 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-display font-bold text-sm text-slate-900 dark:text-slate-100 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors truncate">Complaints</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $this->stats['pending'] }} need attention</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-400 dark:text-slate-600 group-hover:text-red-600 dark:group-hover:text-red-400 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>

    </div>

</div>
