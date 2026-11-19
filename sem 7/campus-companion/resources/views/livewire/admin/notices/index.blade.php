<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Notice;

return new #[Layout('components.layouts.admin')] class extends Component {

    public $search = '';
    public $categoryFilter = 'all';

    public function delete($id)
    {
        Notice::findOrFail($id)->delete();
        session()->flash('success', 'Notice deleted successfully');
    }

    public function getNoticesProperty()
    {
        return Notice::with(['department', 'classRoom'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryFilter !== 'all', function ($q) {
                $q->where('category', $this->categoryFilter);
            })
            ->latest()
            ->get();
    }
};
?>

<div class="space-y-8 pb-12">

    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0f172a] via-[#0b1120] to-[#080c14] border border-white/5 p-8 shadow-xl">
        <div class="absolute inset-0 opacity-20" style="background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:40px 40px;"></div>
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-[#d2e823]/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#d2e823]/10 border border-[#d2e823]/20 text-[#d2e823] text-xs font-semibold mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#d2e823] animate-pulse"></span>
                    Broadcast Center
                </div>
                <h1 class="font-display font-extrabold text-3xl text-white tracking-tight leading-tight">
                    Campus Notices
                </h1>
                <p class="text-slate-400 text-sm mt-1">Broadcast official announcements to students, faculty, and departments.</p>
            </div>

            <a href="/admin/notices/create"
               class="inline-flex items-center gap-2 bg-[#d2e823] text-black font-bold text-xs px-5 py-3 rounded-xl hover:opacity-90 active:scale-[0.98] transition shadow-lg shadow-[#d2e823]/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                New Notice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-2xl text-sm font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- SEARCH & FILTER BAR -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
            <input wire:model.live.debounce.300ms="search" placeholder="Search notices..."
                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 shadow-sm dark:shadow-none transition" />
        </div>

        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            @foreach(['all', 'general', 'academic', 'administrative'] as $cat)
                <button wire:click="$set('categoryFilter', '{{ $cat }}')"
                    class="px-4 py-2 rounded-xl text-xs font-semibold transition-all shadow-sm dark:shadow-none {{ $categoryFilter === $cat ? 'bg-[#d2e823] text-black' : 'bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300' }}">
                    {{ ucfirst($cat) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- LIST -->
    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl overflow-hidden shadow-sm dark:shadow-lg divide-y divide-slate-100 dark:divide-white/5">
        @forelse($this->notices as $n)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 hover:bg-slate-50 dark:hover:bg-[#0f172a] transition-all duration-200 group">
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0
                        {{ $n->category === 'academic' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : ($n->category === 'administrative' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-lime-500/10 text-lime-700 dark:text-[#d2e823] border border-lime-500/20 dark:border-[#d2e823]/20') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-lime-600 dark:group-hover:text-[#d2e823] transition-colors leading-snug">
                                {{ $n->title }}
                            </h3>
                            <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                {{ $n->category === 'academic' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : ($n->category === 'administrative' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-lime-500/10 text-lime-700 dark:text-[#d2e823] border border-lime-500/20 dark:border-[#d2e823]/20') }}">
                                {{ $n->category }}
                            </span>
                        </div>

                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $n->description }}
                        </p>

                        <div class="flex items-center gap-3 mt-2 text-xs text-slate-400 dark:text-slate-500">
                            <span>{{ $n->created_at?->diffForHumans() }}</span>
                            @if($n->department)
                                <span>•</span>
                                <span class="text-slate-600 dark:text-slate-400 font-medium">{{ $n->department->name }}</span>
                            @endif
                            @if($n->classRoom)
                                <span>•</span>
                                <span class="text-slate-600 dark:text-slate-400 font-medium">{{ $n->classRoom->year }} {{ $n->classRoom->name }}</span>
                            @endif
                            @if(!$n->department_id && !$n->class_room_id)
                                <span>•</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium">All Campus</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <a href="/admin/notices/edit/{{ $n->id }}"
                        class="px-4 py-2 text-xs font-semibold bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl transition">
                        Edit
                    </a>

                    <button wire:click="delete({{ $n->id }})"
                        wire:confirm="Are you sure you want to delete this notice?"
                        class="px-4 py-2 text-xs font-semibold bg-red-500/10 hover:bg-red-500/20 text-red-500 dark:text-red-400 rounded-xl transition">
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="py-16 text-center p-8">
                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-3 text-slate-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">No notices found</h3>
                <p class="text-slate-500 text-xs mt-1">Broadcast your first notice to campus members.</p>
            </div>
        @endforelse
    </div>

</div>

