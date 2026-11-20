<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Complaint;
use App\Models\Notification;

return new #[Layout('components.layouts.student')] class extends Component {
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $type = 'lab';
    public $photo;
    public $selectedComplaint = null;

    public function openModal($id)
    {
        $this->selectedComplaint = Complaint::with(['user', 'assignedTeacher'])
            ->where('user_id', auth()->id())
            ->find($id);
    }

    public function closeModal()
    {
        $this->selectedComplaint = null;
    }

    public function submit()
    {
        $this->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'type' => 'required|in:lab,class',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();

        // Optional spam protection (basic)
        $recent = Complaint::where('user_id', $user->id)
            ->where('created_at', '>', now()->subSeconds(10))
            ->exists();

        if ($recent) {
            session()->flash('error', 'Please wait before submitting another complaint.');
            return;
        }

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('complaints', 'public');
        }

        $complaint = Complaint::create([
            'user_id' => $user->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        // Send notification to HOD
        $hodId = $user->department?->head_id;
        if ($hodId) {
            Notification::create([
                'user_id' => $hodId,
                'message' => "New issue raised by {$user->name}: {$this->title}",
            ]);
        }

        session()->flash('success', 'Complaint submitted successfully');

        // better reset
        $this->reset(['title', 'description', 'photo']);
        $this->type = 'lab';
    }

    public function getComplaintsProperty()
    {
        return Complaint::where('user_id', auth()->id())
            ->latest()
            ->get();
    }
};
?>

<div class="min-h-screen space-y-8 pb-16">

    {{-- ── PAGE HEADER ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gradient-to-br dark:from-[#0f172a] dark:to-[#080c14] border border-slate-200 dark:border-white/5 p-8 shadow-sm dark:shadow-xl transition-all">
        <div class="absolute inset-0 opacity-10 dark:opacity-30 pointer-events-none"
            style="background-image: linear-gradient(currentColor 1px,transparent 1px),linear-gradient(90deg,currentColor 1px,transparent 1px); background-size:40px 40px; color: rgba(100,116,139,0.3);">
        </div>
        <div class="absolute -bottom-8 -right-8 w-48 h-48 rounded-full bg-rose-500/10 blur-3xl pointer-events-none">
        </div>

        <div class="relative flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold mb-4">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14.7 6.3a4 4 0 11-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4z" />
                    </svg>
                    Issue Tracker
                </div>
                <h1 class="font-display font-extrabold text-4xl text-slate-900 dark:text-white tracking-tight">Complaints</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">Submit and track your reported issues</p>
            </div>

            {{-- Stats strip --}}
            <div class="flex gap-3">
                @php
                    $all = $this->complaints;
                    $pending = $all->where('status', 'pending')->count();
                    $inProgress = $all->where('status', 'in_progress')->count();
                    $resolved = $all->where('status', 'resolved')->count();
                @endphp
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl px-4 py-2.5 text-center shadow-xs">
                    <div class="font-display font-bold text-xl text-amber-600 dark:text-yellow-400 leading-none">{{ $pending }}</div>
                    <div class="text-[0.65rem] text-slate-500 font-medium mt-1">Pending</div>
                </div>
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl px-4 py-2.5 text-center shadow-xs">
                    <div class="font-display font-bold text-xl text-blue-600 dark:text-blue-400 leading-none">{{ $inProgress }}</div>
                    <div class="text-[0.65rem] text-slate-500 font-medium mt-1">Active</div>
                </div>
                <div class="bg-green-500/10 border border-green-500/20 rounded-2xl px-4 py-2.5 text-center shadow-xs">
                    <div class="font-display font-bold text-xl text-green-600 dark:text-green-400 leading-none">{{ $resolved }}</div>
                    <div class="text-[0.65rem] text-slate-500 font-medium mt-1">Resolved</div>
                </div>
            </div>
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

    <div class="grid lg:grid-cols-5 gap-6">

        {{-- ── FORM ── --}}
        <div class="lg:col-span-2">
            <div class="sticky top-6 bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-6 space-y-5 shadow-sm dark:shadow-none">
                <div>
                    <h2 class="font-display font-bold text-lg text-slate-900 dark:text-white">Raise a Complaint</h2>
                    <p class="text-slate-500 text-xs mt-1">We'll route it to the right department automatically.</p>
                </div>

                {{-- Type selector --}}
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="$set('type','lab')"
                        class="p-3.5 rounded-2xl border text-sm font-semibold transition-all duration-200 text-left shadow-xs
                            {{ $type === 'lab' ? 'bg-lime-500/10 border-lime-500/30 text-lime-700 dark:text-[#d2e823]' : 'bg-slate-50 dark:bg-black/30 border-slate-200 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:border-slate-300 dark:hover:border-white/15' }}">
                        <svg class="w-4 h-4 mb-2 {{ $type === 'lab' ? 'text-lime-600 dark:text-[#d2e823]' : 'text-slate-400 dark:text-slate-500' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" />
                        </svg>
                        Lab Issue
                    </button>
                    <button wire:click="$set('type','class')"
                        class="p-3.5 rounded-2xl border text-sm font-semibold transition-all duration-200 text-left shadow-xs
                            {{ $type === 'class' ? 'bg-lime-500/10 border-lime-500/30 text-lime-700 dark:text-[#d2e823]' : 'bg-slate-50 dark:bg-black/30 border-slate-200 dark:border-white/5 text-slate-600 dark:text-slate-400 hover:border-slate-300 dark:hover:border-white/15' }}">
                        <svg class="w-4 h-4 mb-2 {{ $type === 'class' ? 'text-lime-600 dark:text-[#d2e823]' : 'text-slate-400 dark:text-slate-500' }}"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <path d="M8 21h8M12 17v4" />
                        </svg>
                        Class Issue
                    </button>
                </div>

                {{-- Title --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Title</label>
                    <input wire:model="title" placeholder="Brief description of the issue"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition" />
                    @error('title')
                        <p class="text-rose-500 text-xs flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Details</label>
                    <textarea wire:model="description" placeholder="Describe the problem in detail..." rows="4"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition resize-none"></textarea>
                    @error('description')
                        <p class="text-rose-500 text-xs flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Photo --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Photo Proof (Optional)</label>
                    <input type="file" wire:model="photo" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-lime-500/10 file:text-lime-700 dark:file:bg-[#d2e823]/10 dark:file:text-[#d2e823] hover:file:bg-lime-500/20" />
                    <div wire:loading wire:target="photo" class="text-xs text-slate-500 mt-1">Uploading preview...</div>
                    @error('photo')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if ($photo)
                        <div class="mt-2 relative w-full h-32 rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-black/40">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover" />
                        </div>
                    @endif
                </div>

                <button wire:click="submit"
                    class="w-full bg-[#d2e823] text-black py-3 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] transition-all duration-150 flex items-center justify-center gap-2 shadow-md shadow-[#d2e823]/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                    </svg>
                    Submit Complaint
                </button>

                @if (session('success'))
                    <div
                        class="flex items-center gap-2 bg-green-500/15 border border-green-500/25 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl text-sm font-medium">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ── COMPLAINT LIST ── --}}
        <div class="lg:col-span-3 space-y-3">

            <h2 class="font-display font-bold text-slate-800 dark:text-white text-sm uppercase tracking-wider">Your Complaints</h2>

            @forelse($this->complaints as $i => $c)
                <div wire:click="openModal({{ $c->id }})"
                    class="group bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-5 hover:bg-slate-50 dark:hover:bg-[#0f172a] hover:border-slate-300 dark:hover:border-white/10 transition-all duration-200 overflow-hidden shadow-sm dark:shadow-none cursor-pointer"
                    style="animation: fadeUp 0.4s ease both; animation-delay: {{ $i * 0.06 }}s">
                    <div class="flex justify-between items-start gap-4">

                        <div class="flex-1 min-w-0">
                            {{-- Type + time --}}
                            <div class="flex items-center gap-2 mb-1.5">
                                <span
                                    class="text-[0.65rem] font-bold uppercase tracking-widest text-slate-500">{{ ucfirst($c->type) }}
                                    Issue</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                <span
                                    class="text-[0.65rem] text-slate-500 dark:text-slate-600">{{ $c->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white/90 group-hover:text-slate-950 dark:group-hover:text-white transition">
                                {{ $c->title }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">{{ $c->description }}
                            </p>
                            @if($c->photo)
                                <div class="mt-3 flex items-center gap-3">
                                    <img src="{{ asset('storage/' . $c->photo) }}"
                                         alt="Proof"
                                         class="w-16 h-12 object-cover rounded-lg border border-slate-200 dark:border-white/10 shrink-0"
                                         onerror="this.style.display='none'" />
                                    <span class="inline-flex items-center gap-1.5 text-xs text-lime-600 dark:text-[#d2e823] font-semibold hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        View Details & Attachment
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Status pill --}}
                        <div class="flex-shrink-0">
                            @if ($c->status === 'pending')
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-yellow-400 text-[0.65rem] font-bold uppercase tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-yellow-400 animate-pulse"></span>Pending
                                </span>
                            @elseif($c->status === 'in_progress')
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[0.65rem] font-bold uppercase tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse"></span>In Progress
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-green-500/10 border border-green-500/20 text-green-700 dark:text-green-400 text-[0.65rem] font-bold uppercase tracking-wide">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>Resolved
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- Progress bar for in-progress --}}
                    @if ($c->status === 'in_progress')
                        <div class="mt-4 h-1 w-full rounded-full bg-slate-100 dark:bg-white/5">
                            <div class="h-1 w-2/3 rounded-full bg-blue-500 dark:bg-blue-400 transition-all duration-1000"></div>
                        </div>
                    @endif

                </div>

            @empty

                <div class="flex flex-col items-center justify-center py-24 text-center bg-white dark:bg-[#0f172a]/40 border border-slate-200 dark:border-white/5 rounded-3xl shadow-sm dark:shadow-none">
                    <div
                        class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-[#0f172a] border border-slate-200 dark:border-white/5 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium">No complaints yet</p>
                    <p class="text-slate-400 dark:text-slate-600 text-xs mt-1">Submit an issue using the form</p>
                </div>
            @endforelse

        </div>

    </div>

    {{-- ── COMPLAINT DETAIL & ATTACHMENT MODAL ── --}}
    @if($selectedComplaint)
    <div class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-3xl p-6 md:p-8 max-w-xl w-full space-y-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
                <div>
                    <span class="text-[0.65rem] font-bold uppercase tracking-widest text-slate-500">{{ ucfirst($selectedComplaint->type) }} Issue</span>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $selectedComplaint->title }}</h3>
                </div>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/5 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</p>
                    @if ($selectedComplaint->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-yellow-400 text-xs font-bold uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-yellow-400 animate-pulse"></span>Pending Redressal
                        </span>
                    @elseif($selectedComplaint->status === 'in_progress')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse"></span>In Progress
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-green-500/10 border border-green-500/20 text-green-700 dark:text-green-400 text-xs font-bold uppercase tracking-wide">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>Resolved
                        </span>
                    @endif
                </div>

                @if($selectedComplaint->assignedTeacher)
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Assigned Teacher</p>
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $selectedComplaint->assignedTeacher->name }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Description</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-black/30 p-4 rounded-2xl border border-slate-100 dark:border-white/5 whitespace-pre-wrap">{{ $selectedComplaint->description }}</p>
                </div>

                @if($selectedComplaint->photo)
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Attached Proof</p>
                    <a href="{{ asset('storage/' . $selectedComplaint->photo) }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-slate-200 dark:border-white/10 hover:border-lime-500/50 transition">
                        <img src="{{ asset('storage/' . $selectedComplaint->photo) }}"
                             alt="Complaint Proof"
                             class="w-full max-h-72 object-contain bg-black/40 rounded-xl" />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 text-white text-xs font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Click to View Full Size Image
                        </div>
                    </a>
                </div>
                @endif
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-white/5">
                <button wire:click="closeModal" class="bg-[#d2e823] text-black px-5 py-2 rounded-xl text-xs font-bold hover:opacity-90 active:scale-95 transition">
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
