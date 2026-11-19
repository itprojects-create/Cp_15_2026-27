<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Notice;
use App\Models\NoticeRead;
use App\Models\User;
use App\Models\Notification;

return new #[Layout('components.layouts.teacher')] class extends Component {

    public $notices = [];
    public $filter = 'all';

    public $selectedNotice = null;
    public $readStats = [];
    public $activeModalTab = 'stats'; // 'stats', 'content', 'read_list', 'unread_list'
    public $reminderSent = false;

    public function mount()
    {
        $this->loadNotices();
    }

    public function updatedFilter()
    {
        $this->loadNotices();
    }

    public function loadNotices()
    {
        $user = auth()->user();

        $query = Notice::query()->latest();

        if ($this->filter !== 'all') {
            $query->where('category', $this->filter);
        }

        $query->where(function ($q) use ($user) {
            $q->whereNull('department_id');

            if ($user->department_id) {
                $q->orWhere('department_id', $user->department_id);
            }
        });

        $this->notices = $query->get();
    }

    public function open($id, $tab = 'stats')
    {
        $this->selectedNotice = Notice::find($id);
        $this->activeModalTab = $tab;
        $this->reminderSent = false;
        $this->loadReadStats();
    }

    public function loadReadStats()
    {
        if (!$this->selectedNotice) return;

        // Target students based on notice scope
        $query = User::where('role', 'student');

        if ($this->selectedNotice->department_id) {
            $query->where('department_id', $this->selectedNotice->department_id);
        }
        if ($this->selectedNotice->class_room_id) {
            $query->where('class_room_id', $this->selectedNotice->class_room_id);
        }

        $allStudents = $query->get();
        $reads = NoticeRead::with('user')
            ->where('notice_id', $this->selectedNotice->id)
            ->latest()
            ->get();

        $readUserMap = $reads->keyBy('user_id');

        $readList = [];
        $unreadList = [];

        foreach ($allStudents as $student) {
            if ($readUserMap->has($student->id)) {
                $readEntry = $readUserMap->get($student->id);
                $readList[] = [
                    'name' => $student->name,
                    'email' => $student->email,
                    'read_at' => $readEntry->created_at ? $readEntry->created_at->diffForHumans() : 'Recently',
                ];
            } else {
                $unreadList[] = [
                    'name' => $student->name,
                    'email' => $student->email,
                ];
            }
        }

        $total = count($allStudents);
        $readCount = count($readList);
        $percentage = $total > 0 ? round(($readCount / $total) * 100, 1) : 0;

        $this->readStats = [
            'total' => $total,
            'read_count' => $readCount,
            'unread_count' => count($unreadList),
            'percentage' => $percentage,
            'read_list' => $readList,
            'unread_list' => $unreadList,
        ];
    }

    public function dispatchReminder()
    {
        if (!$this->selectedNotice || empty($this->readStats['unread_list'])) return;

        $unreadEmails = collect($this->readStats['unread_list'])->pluck('email')->toArray();
        $targetUsers = User::whereIn('email', $unreadEmails)->get();

        foreach ($targetUsers as $student) {
            Notification::firstOrCreate([
                'user_id' => $student->id,
                'message' => "Urgent Notice Reminder: Please review '{$this->selectedNotice->title}' immediately.",
            ]);
        }

        $this->reminderSent = true;
    }

    public function close()
    {
        $this->selectedNotice = null;
        $this->readStats = [];
        $this->reminderSent = false;
    }
};
?>

<div class="space-y-8">

    <!-- HERO HEADER: PROPER LIGHT/DARK STYLING -->
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:via-[#0b1120] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-7 shadow-sm dark:shadow-xl transition-all">
        <!-- Subtle Grid Background -->
        <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none" style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-lime-500/10 dark:bg-[#d2e823]/10 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-500/10 dark:bg-[#d2e823]/10 border border-lime-500/25 dark:border-[#d2e823]/25 text-lime-700 dark:text-[#d2e823] text-xs font-bold mb-3">
                    <span class="w-2 h-2 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></span>
                    Faculty Broadcast &amp; Accountability
                </div>
                <!-- High Contrast Title in Both Light and Dark Modes -->
                <h1 class="font-display font-extrabold text-3xl md:text-4xl text-slate-900 dark:text-white tracking-tight leading-tight">
                    Notice Broadcast &amp; Read Tracking
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm mt-1.5 max-w-2xl font-medium">
                    Publish departmental announcements, track student reading accountability in real-time, and monitor compliance.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="/teacher/notices/create"
                    class="bg-[#d2e823] text-black font-extrabold text-xs px-5 py-2.5 rounded-xl hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-lime-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Publish New Notice
                </a>
            </div>
        </div>
    </div>

    <!-- FILTERS BAR: LIGHT & DARK MODE COMPLIANT -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 p-4 rounded-2xl shadow-sm">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Filter Category:</span>
            <div class="flex gap-1.5">
                @foreach (['all' => 'All Notices', 'academic' => 'Academic', 'administrative' => 'Administrative', 'general' => 'General'] as $catKey => $catLabel)
                    <button wire:click="$set('filter', '{{ $catKey }}')"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all
                        {{ $filter === $catKey ? 'bg-[#d2e823] text-black border border-lime-400 shadow-sm' : 'bg-slate-100 dark:bg-[#080c14] border border-slate-200 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        {{ $catLabel }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
            Showing <span class="text-slate-900 dark:text-[#d2e823] font-bold">{{ count($notices) }}</span> Active Broadcasts
        </div>
    </div>

    <!-- NOTICES LIST -->
    <div class="space-y-4">
        @forelse($notices as $n)
            @php
                $readsCount = \App\Models\NoticeRead::where('notice_id', $n->id)->count();
                $totalTarget = 300; // Total student cohort
                $pct = min(100, round(($readsCount / $totalTarget) * 100, 1));
            @endphp
            <div class="group bg-white dark:bg-[#0f172a]/80 border border-slate-200 dark:border-white/5 rounded-3xl p-6 hover:border-slate-300 dark:hover:border-white/15 transition-all shadow-sm">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    
                    <!-- Notice Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-lg tracking-wider
                                @if ($n->category === 'academic') bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/15 dark:text-blue-400 dark:border-blue-500/20
                                @elseif($n->category === 'administrative') bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-500/15 dark:text-purple-400 dark:border-purple-500/20
                                @else bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-lime-500/15 dark:text-lime-400 dark:border-lime-500/20 @endif">
                                {{ ucfirst($n->category) }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6l4 2"/></svg>
                                {{ $n->created_at->diffForHumans() }}
                            </span>
                            <span class="text-xs text-slate-300 dark:text-slate-600">•</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Campus Cohort Scope</span>
                        </div>

                        <!-- Clean High Contrast Title -->
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-[#d2e823] transition-colors leading-snug">
                            {{ $n->title }}
                        </h2>

                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 line-clamp-2 leading-relaxed font-normal">
                            {{ $n->description }}
                        </p>
                    </div>

                    <!-- Notice Read Accountability Mini Tracker Widget -->
                    <div class="lg:w-80 bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 rounded-2xl p-4 shrink-0 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Read Accountability
                            </span>
                            <span class="text-xs font-mono font-extrabold text-slate-900 dark:text-[#d2e823]">
                                {{ $readsCount }} / {{ $totalTarget }} ({{ $pct }}%)
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-white/10 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-[#84cc16] dark:to-[#d2e823] h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <!-- High Contrast Audit Button in Light and Dark -->
                            <button wire:click="open({{ $n->id }}, 'stats')"
                                class="btn-audit-tracker px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-[#d2e823] text-black hover:bg-lime-400 border border-lime-400 shadow-xs flex items-center gap-1.5 transition">
                                <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Audit Tracker</span>
                            </button>

                            <button wire:click="open({{ $n->id }}, 'content')"
                                class="text-xs font-bold text-blue-600 dark:text-[#d2e823] hover:underline">
                                View Full Notice &rarr;
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        @empty
            <div class="text-center py-20 bg-white dark:bg-[#0f172a]/40 border border-slate-200 dark:border-white/5 rounded-3xl p-8">
                <p class="text-slate-500 dark:text-slate-400 text-sm">No notices found for this filter category.</p>
            </div>
        @endforelse
    </div>

    <!-- ================= NOTICE READ ACCOUNTABILITY TRACKER MODAL ================= -->
    @if($selectedNotice)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-md" wire:click="close"></div>

            <!-- Modal Window: Light and Dark Mode Compliant -->
            <div class="relative bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-3xl w-full max-w-2xl z-10 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Top Header: Light in Light Mode, Dark in Dark Mode -->
                <div class="bg-slate-50 dark:bg-[#080c14] border-b border-slate-200 dark:border-white/10 p-6 flex items-center justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-lime-500/10 dark:bg-[#d2e823]/15 text-lime-700 dark:text-[#d2e823] border border-lime-500/25 dark:border-[#d2e823]/30 text-[10px] font-extrabold uppercase tracking-widest mb-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-lime-500 dark:bg-[#d2e823] animate-pulse"></span>
                            Notice Read Accountability Tracker
                        </div>
                        <h2 class="text-xl font-bold font-display leading-tight text-slate-900 dark:text-white">{{ $selectedNotice->title }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Published {{ $selectedNotice->created_at->format('d M Y \a\t h:i A') }} • Target Cohort: Campus Students</p>
                    </div>

                    <button wire:click="close" class="p-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-xl bg-slate-200/60 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Sub-Tabs -->
                <div class="flex border-b border-slate-200 dark:border-white/10 px-6 bg-slate-50 dark:bg-[#080c14] gap-4">
                    <button wire:click="$set('activeModalTab', 'stats')"
                        class="py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 {{ $activeModalTab === 'stats' ? 'border-slate-900 text-slate-900 dark:border-[#d2e823] dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>📊 Compliance Overview</span>
                    </button>
                    <button wire:click="$set('activeModalTab', 'read_list')"
                        class="py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 {{ $activeModalTab === 'read_list' ? 'border-emerald-600 text-emerald-700 dark:border-emerald-400 dark:text-emerald-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>✓ Confirmed Reads ({{ count($readStats['read_list'] ?? []) }})</span>
                    </button>
                    <button wire:click="$set('activeModalTab', 'unread_list')"
                        class="py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 {{ $activeModalTab === 'unread_list' ? 'border-rose-600 text-rose-700 dark:border-rose-400 dark:text-rose-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>⚠️ Pending Unread ({{ count($readStats['unread_list'] ?? []) }})</span>
                    </button>
                    <button wire:click="$set('activeModalTab', 'content')"
                        class="py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 {{ $activeModalTab === 'content' ? 'border-blue-600 text-blue-700 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>📄 Notice Text</span>
                    </button>
                </div>

                <!-- Modal Scrollable Body -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1 text-slate-900 dark:text-white">
                    
                    @if($reminderSent)
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/25 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            Notification reminders successfully dispatched to all {{ count($readStats['unread_list']) }} unread students!
                        </div>
                    @endif

                    <!-- TAB 1: COMPLIANCE STATS OVERVIEW -->
                    @if($activeModalTab === 'stats')
                        <!-- 4 Stat Metric Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 text-center">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Read Compliance</p>
                                <p class="text-2xl font-black font-mono text-emerald-600 dark:text-[#d2e823] mt-1">{{ $readStats['percentage'] ?? 0 }}%</p>
                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">Audited &amp; Live</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 text-center">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Confirmed Reads</p>
                                <p class="text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400 mt-1">{{ $readStats['read_count'] ?? 0 }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Timestamp Logged</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 text-center">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Unread</p>
                                <p class="text-2xl font-black font-mono text-rose-600 dark:text-rose-400 mt-1">{{ $readStats['unread_count'] ?? 0 }}</p>
                                <p class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold mt-0.5">Action Needed</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 text-center">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Target Cohort</p>
                                <p class="text-2xl font-black font-mono text-slate-800 dark:text-slate-300 mt-1">{{ $readStats['total'] ?? 0 }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Enrolled Students</p>
                            </div>
                        </div>

                        <!-- Large Visual Progress Bar -->
                        <div class="space-y-2 p-5 rounded-2xl bg-slate-50 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-600 dark:text-slate-400 uppercase tracking-wider">Overall Cohort Penetration:</span>
                                <span class="text-slate-900 dark:text-[#d2e823] font-mono">{{ $readStats['read_count'] ?? 0 }} of {{ $readStats['total'] ?? 0 }} students confirmed</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-white/10 h-3.5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-500 via-lime-500 to-[#84cc16] dark:to-[#d2e823] h-full rounded-full transition-all duration-700" style="width: {{ $readStats['percentage'] ?? 0 }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-500 font-medium pt-1">
                                <span>Zero Delay Receipt Logging</span>
                                <span>48h Automated Cron Reminder: Active</span>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-xs">
                            <div>
                                <span class="font-bold text-rose-700 dark:text-rose-400 block">{{ $readStats['unread_count'] ?? 0 }} students have not opened this circular</span>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">Dispatching in-app alerts sends an instant push notification to their portals.</span>
                            </div>
                            <button type="button" wire:click="dispatchReminder"
                                class="px-4 py-2.5 rounded-xl text-xs font-extrabold bg-rose-600 text-white hover:bg-rose-700 active:scale-95 transition shadow-md shadow-rose-600/20 shrink-0">
                                Dispatch Reminder Now
                            </button>
                        </div>
                    @endif

                    <!-- TAB 2: CONFIRMED READS LIST -->
                    @if($activeModalTab === 'read_list')
                        <div class="space-y-2">
                            <div class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Students With Timestamped Read Receipts ({{ count($readStats['read_list'] ?? []) }})</div>
                            <div class="max-h-72 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-100 dark:divide-white/5">
                                @forelse($readStats['read_list'] as $item)
                                    <div class="flex items-center justify-between py-2 text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white">{{ $item['name'] }}</p>
                                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $item['email'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-semibold px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10">{{ $item['read_at'] }}</span>
                                    </div>
                                @empty
                                    <p class="text-slate-500 text-xs py-4 text-center">No students have read this notice yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- TAB 3: PENDING UNREAD LIST -->
                    @if($activeModalTab === 'unread_list')
                        <div class="space-y-2">
                            <div class="text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider mb-2">Students Awaiting Notice Review ({{ count($readStats['unread_list'] ?? []) }})</div>
                            <div class="max-h-72 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-100 dark:divide-white/5">
                                @forelse($readStats['unread_list'] as $item)
                                    <div class="flex items-center justify-between py-2 text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 flex items-center justify-center font-bold text-[10px]">!</div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white">{{ $item['name'] }}</p>
                                                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $item['email'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-rose-700 dark:text-rose-400 uppercase px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20">Reminder Pending</span>
                                    </div>
                                @empty
                                    <p class="text-emerald-700 dark:text-emerald-400 text-xs py-4 text-center font-bold">100% compliance! All enrolled students have read this notice.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- TAB 4: FULL NOTICE TEXT -->
                    @if($activeModalTab === 'content')
                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-white/10">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-[#d2e823] uppercase tracking-wider mb-2">Official Circular Body</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed whitespace-pre-wrap font-sans">{{ $selectedNotice->description }}</p>
                        </div>
                    @endif

                </div>

                <!-- Modal Bottom Bar -->
                <div class="p-4 border-t border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-slate-900/60 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">Status: Read Audit Synchronized</span>
                    <button wire:click="close"
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white dark:bg-[#d2e823] dark:text-black hover:opacity-90 transition">
                        Done
                    </button>
                </div>

            </div>

        </div>
    @endif

</div>
