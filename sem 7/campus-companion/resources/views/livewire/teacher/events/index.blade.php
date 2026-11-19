<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;

return new #[Layout('components.layouts.teacher')] class extends Component {

    public $events = [];
    public $selectedEvent = null;

    public function mount()
    {
        $user = auth()->user();

        if (!$user) return;

        $this->events = Event::where(function ($q) use ($user) {
                $q->whereNull('department_id');

                if ($user->department_id) {
                    $q->orWhere('department_id', $user->department_id);
                }
            })
            ->orderBy('date')
            ->get();
    }

    public function open($id)
    {
        $this->selectedEvent = Event::find($id);
    }

    public function close()
    {
        $this->selectedEvent = null;
    }
};
?>

<div class="space-y-10">

    <!-- HEADER -->
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Events</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                View upcoming events relevant to your department
            </p>
        </div>

        <div class="text-sm text-slate-500 dark:text-slate-400 font-medium">
            {{ count($events) }} Events
        </div>
    </div>

    <!-- GRID -->
    <div class="grid md:grid-cols-2 gap-6">

        @forelse($events as $e)

            <div class="group bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/10 transition-all duration-200 flex flex-col justify-between shadow-sm dark:shadow-none">

                <!-- TOP -->
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white/90 group-hover:text-slate-950 dark:group-hover:text-white transition">
                        {{ $e->title }}
                    </h2>

                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                        {{ $e->description }}
                    </p>
                </div>

                <!-- META -->
                <div class="mt-5 flex items-center justify-between text-xs">

                    <div class="text-slate-500 dark:text-slate-400 font-medium">
                        {{ \Carbon\Carbon::parse($e->date)->format('d M Y') }}
                    </div>

                    <span class="px-2.5 py-1 rounded-full bg-blue-500/15 text-blue-600 dark:text-blue-400 font-semibold">
                        {{ $e->location }}
                    </span>

                </div>

                <!-- FOOTER -->
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5 flex justify-between items-center text-xs text-slate-400 dark:text-slate-500">

                    <span>
                        {{ \Carbon\Carbon::parse($e->date)->diffForHumans() }}
                    </span>

                    <button wire:click="open({{ $e->id }})"
                        class="text-lime-600 dark:text-[#d2e823] font-semibold hover:underline">
                        View
                    </button>

                </div>

            </div>

        @empty

            <div class="col-span-2 flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-[#0f172a]/40 border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm dark:shadow-none">

                <svg class="w-10 h-10 text-slate-400 dark:text-slate-500 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                </svg>

                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                    No events available
                </p>

            </div>

        @endforelse

    </div>

    <!-- MODAL -->
    @if($selectedEvent)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <!-- BACKDROP -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                 wire:click="close"></div>

            <!-- CARD -->
            <div class="relative bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-3xl p-6 w-full max-w-lg z-10 shadow-2xl space-y-4">

                <!-- TITLE -->
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-snug">
                        {{ $selectedEvent->title }}
                    </h2>

                    <!-- DATE & LOCATION -->
                    <div class="flex items-center gap-3 mt-2 text-xs text-slate-500">
                        <span>{{ \Carbon\Carbon::parse($selectedEvent->date)->format('d M Y \a\t h:i A') }}</span>
                        <span>•</span>
                        <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $selectedEvent->location }}</span>
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-black/20 p-4 rounded-xl border border-slate-200 dark:border-white/[0.02] max-h-60 overflow-y-auto whitespace-pre-wrap">
                    {{ $selectedEvent->description }}
                </p>

                <!-- FOOTER -->
                <div class="flex justify-end pt-2">
                    <button wire:click="close"
                        class="bg-[#d2e823] text-black px-4 py-2 rounded-xl font-bold text-xs hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                        Close
                    </button>
                </div>

            </div>

        </div>
    @endif

</div>
