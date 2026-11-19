<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Complaint;
use App\Models\Notification;

return new #[Layout('components.layouts.teacher')] class extends Component {

    public $filter = 'all';
    public $selectedComplaint = null;

    public function baseQuery()
    {
        $user = auth()->user();

        return Complaint::whereHas('user', function ($q) use ($user) {
            $q->where('department_id', $user->department_id);
        });
    }

    public function resolve($id)
    {
        $complaint = Complaint::findOrFail($id);

        // Ensure teacher can only resolve complaints from their own department
        $user = auth()->user();
        if ($complaint->user && $complaint->user->department_id !== $user->department_id) {
            session()->flash('error', 'You are not authorized to resolve this complaint.');
            return;
        }

        $complaint->update([
            'status' => 'resolved'
        ]);

        Notification::create([
            'user_id' => $complaint->user_id,
            'message' => "Your complaint '{$complaint->title}' has been resolved",
            'read' => false
        ]);

        session()->flash('success', 'Complaint marked as resolved');
    }

    public function open($id)
    {
        $this->selectedComplaint = Complaint::with('user')->find($id);
    }

    public function close()
    {
        $this->selectedComplaint = null;
    }

    public function getComplaintsProperty()
    {
        return $this->baseQuery()
            ->when($this->filter !== 'all', fn($q) => $q->where('status', $this->filter))
            ->with('user')
            ->orderByRaw("status = 'resolved'")
            ->latest()
            ->get();
    }
};
?>

<div class="space-y-10">

    <!-- HEADER -->
    <div class="flex justify-between items-end flex-wrap gap-4">

        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Complaints</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                Manage student complaints from your department
            </p>
        </div>

        <div class="text-sm text-slate-500 dark:text-slate-400 font-medium">
            {{ $this->complaints->count() }} Total
        </div>

    </div>

    @if(session('success'))
        <div class="bg-emerald-500/15 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-500/15 border border-rose-500/20 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- FILTER -->
    <div class="flex gap-2 text-sm flex-wrap">
        @foreach(['all','pending','in_progress','resolved'] as $f)
            <button wire:click="$set('filter','{{ $f }}')"
                class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 shadow-sm dark:shadow-none
                {{ $filter === $f ? 'bg-[#d2e823] text-black font-bold' : 'bg-white dark:bg-white/5 border border-slate-200 dark:border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300' }}">
                {{ ucfirst(str_replace('_',' ',$f)) }}
            </button>
        @endforeach
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="bg-green-500/15 border border-green-500/20 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- LIST -->
    <div class="space-y-4">

        @forelse($this->complaints as $c)

            <div class="group bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/10 transition-all duration-200 shadow-sm dark:shadow-none">

                <div class="flex justify-between items-start gap-4">

                    <div class="flex-1 min-w-0">
                        <h2 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-slate-950 dark:group-hover:text-white transition">
                            {{ $c->title }}
                        </h2>

                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                            {{ $c->description }}
                        </p>
                    </div>

                    <span class="text-xs px-3 py-1 rounded-full font-semibold shrink-0
                        @if($c->status === 'pending') bg-amber-500/15 text-amber-700 dark:text-amber-400
                        @elseif($c->status === 'in_progress') bg-blue-500/15 text-blue-600 dark:text-blue-400
                        @else bg-green-500/15 text-green-700 dark:text-green-400
                        @endif
                    ">
                        {{ ucfirst(str_replace('_',' ',$c->status)) }}
                    </span>

                </div>

                <div class="flex justify-between items-center mt-4 text-xs text-slate-400 dark:text-slate-500">

                    <div class="flex gap-4">
                        <span class="font-medium text-slate-500 dark:text-slate-400">{{ ucfirst($c->type) }} Issue</span>
                        <span>•</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">By: {{ $c->user?->name }}</span>
                    </div>

                    <div class="flex gap-4 items-center">
                        <span>{{ $c->created_at->diffForHumans() }}</span>

                        <button wire:click="open({{ $c->id }})"
                            class="text-lime-600 dark:text-[#d2e823] font-semibold hover:underline">
                            View
                        </button>
                    </div>

                </div>

                @if($c->status !== 'resolved')
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-white/5 flex justify-end">
                        <button
                            wire:click="resolve({{ $c->id }})"
                            class="bg-[#d2e823] text-black px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                            Mark as Resolved
                        </button>
                    </div>
                @endif

            </div>

        @empty
            <div class="text-center py-20 text-slate-400 dark:text-slate-500 text-sm bg-white dark:bg-[#0f172a]/40 border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm dark:shadow-none">
                No complaints available
            </div>
        @endforelse

    </div>

    <!-- MODAL -->
    @if($selectedComplaint)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                 wire:click="close"></div>

            <div class="relative bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-3xl p-6 w-full max-w-lg z-10 shadow-2xl space-y-4">

                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-snug">
                        {{ $selectedComplaint->title }}
                    </h2>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Reported by {{ $selectedComplaint->user?->name }} • {{ ucfirst($selectedComplaint->type) }} Issue
                    </p>
                </div>

                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-black/20 p-4 rounded-xl border border-slate-200 dark:border-white/[0.02] max-h-60 overflow-y-auto whitespace-pre-wrap">
                    {{ $selectedComplaint->description }}
                </p>

                @if($selectedComplaint->photo)
                    <div class="mt-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1.5">Attached Proof:</p>
                        <a href="{{ asset('storage/' . $selectedComplaint->photo) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-white/10">
                            <img src="{{ asset('storage/' . $selectedComplaint->photo) }}" class="w-full max-h-60 object-cover" />
                        </a>
                    </div>
                @endif

                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs text-slate-400">
                        {{ $selectedComplaint->created_at->format('d M Y') }}
                    </span>

                    <button wire:click="close"
                        class="bg-[#d2e823] text-black px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                        Close
                    </button>
                </div>

            </div>

        </div>
    @endif

</div>
