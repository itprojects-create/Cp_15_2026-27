<?php

use App\Models\Complaint;
use Livewire\Component;
use Livewire\Attributes\Layout;

return new #[Layout('components.layouts.teacher')] class extends Component {

    public int $pending = 0;
    public int $inProgress = 0;
    public int $resolved = 0;
    public $recent = [];

    public function baseQuery()
    {
        $user = auth()->user();

        if (!$user || !$user->department_id) {
            return Complaint::query()->whereRaw('1=0'); // empty safe query
        }

        return Complaint::whereHas('user', function ($q) use ($user) {
            $q->where('department_id', $user->department_id);
        });
    }

    public function mount()
    {
        $query = $this->baseQuery();

        $this->pending = (clone $query)->where('status', 'pending')->count();
        $this->inProgress = (clone $query)->where('status', 'in_progress')->count();
        $this->resolved = (clone $query)->where('status', 'resolved')->count();

        $this->recent = (clone $query)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();
    }
};
?>

<div class="min-h-screen space-y-8 pb-16">

    {{-- ── PAGE HEADER / WELCOME CARD ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-8 shadow-sm dark:shadow-xl transition-all">
        <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none" style="background-image:linear-gradient(currentColor 1px,transparent 1px),linear-gradient(90deg,currentColor 1px,transparent 1px);background-size:40px 40px;color:rgba(100,116,139,0.3)"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <x-user-avatar size="lg" class="shadow-lg ring-2 ring-amber-400/30 shrink-0" />
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-amber-50 dark:bg-amber-400/10 border border-amber-200 dark:border-amber-400/20 text-amber-700 dark:text-amber-400 text-xs font-semibold mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400 animate-pulse"></span>
                        Faculty Portal • {{ auth()->user()->department?->name ?? 'All Departments' }}
                    </div>
                    <h1 class="font-display font-extrabold text-3xl text-slate-900 dark:text-white tracking-tight leading-tight">
                        Welcome, <span class="text-lime-600 dark:text-[#d2e823]">{{ auth()->user()->name }}</span>
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Manage notices, schedule, and track complaints.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/8 rounded-2xl px-5 py-3 shrink-0">
                <span class="font-display font-bold text-3xl text-lime-600 dark:text-[#d2e823] leading-none">{{ $pending + $inProgress + $resolved }}</span>
                <span class="text-slate-500 dark:text-slate-400 text-xs leading-tight font-medium">Department<br>Complaints</span>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid sm:grid-cols-3 gap-4">

        <div class="group bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:border-yellow-500/40 hover:-translate-y-0.5 transition-all duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center group-hover:bg-yellow-500/20 transition-colors">
                    <svg class="w-5 h-5 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                </div>
                <span class="text-[0.65rem] text-yellow-600 dark:text-yellow-400/70 font-semibold uppercase tracking-wider">Needs action</span>
            </div>
            <p class="font-display font-extrabold text-yellow-500 dark:text-yellow-400 leading-none" style="font-size:2.75rem">{{ $pending }}</p>
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium mt-2 uppercase tracking-wider">Pending</p>
        </div>

        <div class="group bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:border-blue-500/40 hover:-translate-y-0.5 transition-all duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                    <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <span class="text-[0.65rem] text-blue-600 dark:text-blue-400/70 font-semibold uppercase tracking-wider">Active</span>
            </div>
            <p class="font-display font-extrabold text-blue-500 dark:text-blue-400 leading-none" style="font-size:2.75rem">{{ $inProgress }}</p>
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium mt-2 uppercase tracking-wider">In Progress</p>
        </div>

        <div class="group bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:border-green-500/40 hover:-translate-y-0.5 transition-all duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center group-hover:bg-green-500/20 transition-colors">
                    <svg class="w-5 h-5 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>
                </div>
                <span class="text-[0.65rem] text-green-600 dark:text-green-400/70 font-semibold uppercase tracking-wider">Closed</span>
            </div>
            <p class="font-display font-extrabold text-green-500 dark:text-green-400 leading-none" style="font-size:2.75rem">{{ $resolved }}</p>
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium mt-2 uppercase tracking-wider">Resolved</p>
        </div>

    </div>

    {{-- ── QUICK ACTIONS ── --}}
    <div class="grid sm:grid-cols-2 gap-3">
        <a href="/teacher/notices/create" class="group flex items-center gap-4 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4 hover:border-[#d2e823]/40 hover:bg-slate-50 dark:hover:bg-[#0f172a] transition-all duration-200 shadow-sm dark:shadow-none">
            <div class="w-10 h-10 rounded-xl bg-[#d2e823]/10 border border-[#d2e823]/20 flex items-center justify-center flex-shrink-0 group-hover:bg-[#d2e823]/20 transition-colors">
                <svg class="w-5 h-5 text-lime-600 dark:text-[#d2e823]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-white/90 group-hover:text-slate-900 dark:group-hover:text-white">Post a Notice</p>
                <p class="text-xs text-slate-500">Notify your students instantly</p>
            </div>
            <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 ml-auto group-hover:text-lime-600 dark:group-hover:text-[#d2e823]/60 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>

        <a href="/teacher/complaints" class="group flex items-center gap-4 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4 hover:border-yellow-500/40 hover:bg-slate-50 dark:hover:bg-[#0f172a] transition-all duration-200 shadow-sm dark:shadow-none">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center flex-shrink-0 group-hover:bg-yellow-500/20 transition-colors">
                <svg class="w-5 h-5 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-white/90 group-hover:text-slate-900 dark:group-hover:text-white">Review Complaints</p>
                <p class="text-xs text-slate-500">{{ $pending }} pending attention</p>
            </div>
            <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 ml-auto group-hover:text-yellow-500 dark:group-hover:text-yellow-400/60 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- ── RECENT COMPLAINTS ── --}}
    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm dark:shadow-none">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-white/[0.04]">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <span class="font-semibold text-slate-900 dark:text-white/90 text-[0.9rem]">Recent Complaints</span>
            </div>
            <a href="/teacher/complaints" class="inline-flex items-center gap-1 text-[0.72rem] text-lime-600 dark:text-[#d2e823]/60 hover:text-lime-700 dark:hover:text-[#d2e823] transition-colors font-medium">
                View all
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
            @forelse($recent as $c)
                <div class="group flex items-start gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                    <div class="w-1.5 h-1.5 rounded-full mt-2 flex-shrink-0
                        @if($c->status === 'pending') bg-yellow-400
                        @elseif($c->status === 'in_progress') bg-blue-400
                        @else bg-green-400
                        @endif">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-slate-800 dark:text-white/80 text-sm font-medium leading-snug group-hover:text-slate-900 dark:group-hover:text-white transition-colors truncate">
                            {{ $c->title }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[0.62rem] text-slate-400 dark:text-white/30">{{ $c->user?->name }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span class="text-[0.62rem] text-slate-400 dark:text-white/25 capitalize">{{ $c->type }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span class="text-[0.62rem] text-slate-400 dark:text-white/25">{{ $c->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <span class="text-[0.65rem] px-2.5 py-1 rounded-full font-semibold flex-shrink-0
                        @if($c->status === 'pending') bg-yellow-500/15 text-yellow-600 dark:text-yellow-400
                        @elseif($c->status === 'in_progress') bg-blue-500/15 text-blue-600 dark:text-blue-400
                        @else bg-green-500/15 text-green-600 dark:text-green-400
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $c->status)) }}
                    </span>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/[0.03] border border-slate-200 dark:border-white/[0.06] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-slate-400 dark:text-white/15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>
                    <p class="text-slate-400 dark:text-white/25 text-sm">No complaints yet</p>
                </div>
            @endforelse
        </div>

    </div>

</div>
