<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Department;

new #[Layout('components.layouts.admin')] class extends Component {

    public $search = '';
    public $status = 'all';
    public $department_id = '';

    public $selectedComplaint = null;
    public $departments = [];
    public $teachers = [];

    public function mount()
    {
        $this->departments = Department::all();
        $this->teachers = User::where('role', 'teacher')->get();
    }

    public function getComplaintsProperty()
    {
        return Complaint::with(['user.department','assignedTeacher'])
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->department_id, function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('department_id', $this->department_id);
                });
            })
            ->latest()
            ->get();
    }

    public function assign($complaintId, $teacherId)
    {
        if (!$teacherId) return;

        $complaint = Complaint::find($complaintId);

        if ($complaint) {
            $complaint->update([
                'assigned_to' => $teacherId,
                'status' => 'in_progress'
            ]);
        }
    }

    // renamed from "view" → FIXED
    public function show($id)
    {
        $this->selectedComplaint = Complaint::with(['user','assignedTeacher'])->find($id);
    }

    public function closeModal()
    {
        $this->selectedComplaint = null;
    }
};
?>

<div class="space-y-8">

    <!-- HEADER -->
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Complaints</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Manage, triage, and assign student complaints across departments
        </p>
    </div>

    <!-- FILTERS -->
    <div class="bg-white dark:bg-[#0f172a] p-4 rounded-2xl border border-slate-200 dark:border-white/5 flex gap-3 flex-wrap shadow-sm dark:shadow-none">

        <!-- SEARCH -->
        <input
            wire:model.live.debounce.300ms="search"
            placeholder="Search complaints..."
            class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 w-full sm:w-60 transition"
        />

        <!-- STATUS -->
        <select wire:model.live="status" class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved</option>
        </select>

        <!-- DEPARTMENT -->
        <select wire:model.live="department_id" class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
            <option value="">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>

    </div>

    <!-- LIST -->
    <div class="space-y-4">

        @forelse($this->complaints as $c)

        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:bg-slate-50 dark:hover:bg-[#0c1324] transition shadow-sm dark:shadow-none">

            <!-- TOP -->
            <div class="flex justify-between items-start gap-4">

                <div>
                    <h2 class="font-bold text-lg text-slate-900 dark:text-white leading-snug">
                        {{ $c->title }}
                    </h2>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        {{ $c->user?->name }} • {{ $c->user?->department?->name ?? 'Global' }}
                    </p>
                </div>

                <!-- STATUS -->
                <span class="text-xs px-3 py-1 rounded-full font-semibold shrink-0
                    @if($c->status === 'pending') bg-amber-500/15 text-amber-700 dark:text-yellow-400
                    @elseif($c->status === 'in_progress') bg-blue-500/15 text-blue-600 dark:text-blue-400
                    @else bg-green-500/15 text-green-700 dark:text-green-400
                    @endif
                ">
                    {{ ucfirst(str_replace('_',' ',$c->status)) }}
                </span>

            </div>

            <!-- DESCRIPTION -->
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-3 line-clamp-2 leading-relaxed">
                {{ $c->description }}
            </p>

            <!-- ASSIGNED -->
            @if($c->assignedTeacher)
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-2">
                    Assigned to: {{ $c->assignedTeacher->name }}
                </p>
            @endif

            <!-- ACTIONS -->
            <div class="flex justify-between items-center mt-4 pt-3 border-t border-slate-100 dark:border-white/5 flex-wrap gap-2">

                <!-- ASSIGN TEACHER -->
                <select
                    wire:change="assign({{ $c->id }}, $event.target.value)"
                    class="bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40"
                >
                    <option value="">Assign Teacher</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $c->assigned_to == $t->id ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->department?->name ?? 'Faculty' }})
                        </option>
                    @endforeach
                </select>

                <!-- VIEW -->
                <button
                    wire:click="show({{ $c->id }})"
                    class="text-lime-600 dark:text-[#d2e823] text-xs font-bold hover:underline"
                >
                    View Details
                </button>

            </div>

        </div>

        @empty

            <div class="text-center py-16 text-slate-400 dark:text-slate-500 text-sm bg-white dark:bg-[#0f172a]/40 border border-slate-200 dark:border-white/5 rounded-3xl shadow-sm dark:shadow-none">
                No complaints found
            </div>

        @endforelse

    </div>

    <!-- MODAL -->
    @if($selectedComplaint)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 p-6 rounded-3xl w-full max-w-lg shadow-2xl space-y-4">

            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-snug">
                    {{ $selectedComplaint->title }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ $selectedComplaint->user?->name }} • {{ $selectedComplaint->user?->department?->name ?? 'Global' }} • {{ ucfirst($selectedComplaint->type) }} Issue
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

            <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                <span>{{ $selectedComplaint->created_at->format('d M Y \a\t h:i A') }}</span>
                @if($selectedComplaint->assignedTeacher)
                    <span class="text-blue-600 dark:text-blue-400 font-medium">Assigned to: {{ $selectedComplaint->assignedTeacher->name }}</span>
                @endif
            </div>

            <div class="flex justify-end pt-2">
                <button
                    wire:click="closeModal"
                    class="bg-[#d2e823] text-black px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10"
                >
                    Close
                </button>
            </div>

        </div>

    </div>
    @endif

</div>
