<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Notice;

return new #[Layout('components.layouts.student')] class extends Component {
    public $notices = [];
    public $search = '';
    public $activeCategory = 'all';

    public $selectedNotice = null;

    public function mount()
    {
        $this->loadNotices();
    }

    public function updatedSearch()
    {
        $this->loadNotices();
    }

    public function filterCategory($cat)
    {
        $this->activeCategory = $cat;
        $this->loadNotices();
    }

    public function loadNotices()
    {
        $user = auth()->user();

        $query = Notice::latest();

        //Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        //Category filter
        if ($this->activeCategory !== 'all') {
            $query->where('category', $this->activeCategory);
        }

        //SCOPE FILTER
        $query->where(function ($q) use ($user) {
            // Global notices
            $q->where(function ($sub) {
                $sub->whereNull('department_id')->whereNull('class_room_id');
            });

            // Department notices
            if ($user->department_id) {
                $q->orWhere(function ($sub) use ($user) {
                    $sub->where('department_id', $user->department_id)
                        ->whereNull('class_room_id');
                });
            }

            // Class notices (students)
            if ($user->class_room_id) {
                $q->orWhere('class_room_id', $user->class_room_id);
            }
        });

        $this->notices = $query->get();
    }

    public function openNotice($id)
    {
        $this->selectedNotice = Notice::find($id);

        if ($this->selectedNotice) {
            \App\Models\NoticeRead::firstOrCreate([
                'user_id' => auth()->id(),
                'notice_id' => $id,
            ]);
        }
    }

    public function closeNotice()
    {
        $this->selectedNotice = null;
    }

    public function getReadNoticeIdsProperty()
    {
        return \App\Models\NoticeRead::where('user_id', auth()->id())
            ->pluck('notice_id')
            ->toArray();
    }
};
?>

<div class="min-h-screen space-y-8 pb-16">

    {{-- ── PAGE HEADER ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-8 shadow-sm dark:shadow-xl transition-all">
        {{-- Grid bg --}}
        <div class="absolute inset-0 opacity-10 dark:opacity-30 pointer-events-none"
            style="background-image: linear-gradient(currentColor 1px,transparent 1px),linear-gradient(90deg,currentColor 1px,transparent 1px); background-size:40px 40px; color: rgba(100,116,139,0.3);">
        </div>
        {{-- Orb --}}
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-[#d2e823]/10 blur-3xl pointer-events-none">
        </div>

        <div class="relative flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-500/10 border border-lime-500/20 text-lime-600 dark:text-[#d2e823] text-xs font-semibold mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></span>
                    Live Updates
                </div>
                <h1 class="font-display font-extrabold text-4xl text-slate-900 dark:text-white tracking-tight">Notices</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">All announcements from your department</p>
            </div>

            <div class="flex items-center gap-2 bg-slate-50 dark:bg-[#d2e823]/10 border border-slate-200 dark:border-[#d2e823]/20 rounded-2xl px-4 py-3 shrink-0">
                <span class="font-display font-bold text-3xl text-lime-600 dark:text-[#d2e823] leading-none">{{ count($notices) }}</span>
                <span class="text-slate-500 dark:text-slate-400 text-xs leading-tight">Total<br>Notices</span>
            </div>
        </div>
    </div>

    {{-- ── SEARCH + FILTERS ── --}}
    <div class="flex flex-col sm:flex-row gap-3">

        {{-- Search --}}
        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" fill="none"
                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
            <input wire:model.live.debounce.300ms="search" placeholder="Search notices..."
                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 shadow-sm dark:shadow-none transition" />
        </div>

        {{-- Category filters --}}
        <div class="flex items-center gap-2 overflow-x-auto">
            @foreach (['all', 'academic', 'administrative', 'general'] as $cat)
                <button wire:click="filterCategory('{{ $cat }}')"
                    class="flex-shrink-0 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm dark:shadow-none {{ $activeCategory === $cat ? 'bg-[#d2e823] text-black font-bold' : 'bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/8 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-white/20' }}">
                    {{ ucfirst($cat) }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── NOTICE LIST ── --}}
    <div class="space-y-3">

        @php
            $readIds = $this->readNoticeIds;
        @endphp

        @forelse($notices as $i => $n)
            @php
                $isRead = in_array($n->id, $readIds);
            @endphp
            <div wire:click="openNotice({{ $n->id }})" class="group relative bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/10 transition-all duration-200 cursor-pointer overflow-hidden shadow-sm dark:shadow-none"
                style="animation: fadeUp 0.4s ease both; animation-delay: {{ $i * 0.05 }}s">
                {{-- Accent bar --}}
                <div
                    class="absolute left-0 top-0 bottom-0 w-1 rounded-full
                    {{ $n->category === 'academic' ? 'bg-blue-500 dark:bg-blue-400' : '' }}
                    {{ $n->category === 'administrative' ? 'bg-purple-500 dark:bg-purple-400' : '' }}
                    {{ $n->category === 'general' ? 'bg-lime-500 dark:bg-[#d2e823]' : '' }}
                    opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                </div>

                <div class="flex justify-between items-start gap-4">

                    <div class="flex-1 min-w-0">
                        {{-- Category + Time --}}
                        <div class="flex items-center gap-2 mb-2">
                            @if(!$isRead)
                                <span class="px-2 py-0.5 rounded bg-lime-500/20 text-lime-700 dark:text-[#d2e823] text-[0.6rem] font-extrabold animate-pulse uppercase tracking-wider">New</span>
                            @endif
                            <span
                                class="text-[0.65rem] font-bold uppercase tracking-widest
                                {{ $n->category === 'academic' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                {{ $n->category === 'administrative' ? 'text-purple-600 dark:text-purple-400' : '' }}
                                {{ $n->category === 'general' ? 'text-lime-600 dark:text-[#d2e823]' : '' }}
                            ">{{ ucfirst($n->category) }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                            <span class="text-[0.65rem] text-slate-500 dark:text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
                        </div>

                        <h2
                            class="text-base font-semibold text-slate-900 dark:text-white/90 group-hover:text-slate-950 dark:group-hover:text-white transition leading-snug">
                            {{ $n->title }}
                        </h2>

                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ $n->description }}
                        </p>
                    </div>

                    {{-- Arrow --}}
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center opacity-0 group-hover:opacity-100 translate-x-1 group-hover:translate-x-0 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 text-lime-600 dark:text-[#d2e823]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </div>

                </div>
            </div>

        @empty

            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div
                    class="w-16 h-16 rounded-2xl bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/5 flex items-center justify-center mb-4 shadow-sm dark:shadow-none">
                    <svg class="w-7 h-7 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path
                            d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="text-slate-600 dark:text-slate-400 text-sm font-medium">No notices found</p>
                <p class="text-slate-400 dark:text-slate-600 text-xs mt-1">Try adjusting your search or filters</p>
            </div>
        @endforelse

    </div>

    {{-- MODAL --}}
    @if($selectedNotice)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- BACKDROP -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeNotice"></div>

            <!-- MODAL CARD -->
            <div class="relative bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-2xl p-6 w-full max-w-lg z-10 space-y-4 shadow-2xl">
                <div>
                    <span class="text-[0.65rem] font-bold uppercase tracking-widest text-lime-600 dark:text-[#d2e823]">{{ ucfirst($selectedNotice->category) }}</span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mt-1 leading-snug">{{ $selectedNotice->title }}</h2>
                    <p class="text-xs text-slate-500 mt-1">{{ $selectedNotice->created_at->format('d M Y \a\t h:i A') }}</p>
                </div>

                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-black/20 p-4 rounded-xl border border-slate-200 dark:border-white/[0.02] max-h-60 overflow-y-auto whitespace-pre-wrap">
                    {{ $selectedNotice->description }}
                </p>

                <div class="flex justify-end pt-2">
                    <button wire:click="closeNotice" class="bg-[#d2e823] text-black px-5 py-2.5 rounded-xl font-bold text-xs hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
