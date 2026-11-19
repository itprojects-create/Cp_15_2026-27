<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Complaint;

return new #[Layout('components.layouts.teacher')] class extends Component {

    use WithFileUploads;

    public $photo;
    public string $name = '';
    public string $email = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
    }

    public function savePhoto()
    {
        $this->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $this->photo->store('profiles', 'public');

        $user->profile_photo = $path;
        $user->save();

        auth()->setUser($user->fresh());

        $this->reset('photo');

        session()->flash('success', 'Profile photo updated successfully');
    }

    public function updateInfo()
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users')->ignore($user->id)
            ],
        ]);

        $user->update($validated);

        auth()->setUser($user->fresh());

        session()->flash('success', 'Profile information updated successfully');
    }

    public function getStatsProperty()
    {
        $user = auth()->user();

        $query = Complaint::whereHas('user', function ($q) use ($user) {
            $q->where('department_id', $user->department_id);
        });

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
        ];
    }
};
?>

<div class="space-y-10">

    <!-- HEADER -->
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Profile Settings</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Manage your account settings and department profile
        </p>
    </div>

    <!-- PROFILE CARD -->
    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-6 md:p-8 space-y-8 shadow-sm dark:shadow-none">

        <!-- TOP SECTION -->
        <div class="flex items-center gap-6 flex-wrap">

            <!-- AVATAR -->
            <div class="relative w-24 h-24 shrink-0">

                @if($photo)
                    <img src="{{ $photo->temporaryUrl() }}"
                         class="w-24 h-24 rounded-full object-cover ring-4 ring-lime-500/40 dark:ring-[#d2e823]/40 shadow-md" />
                @elseif(auth()->user()->profile_photo)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                         class="w-24 h-24 rounded-full object-cover border border-slate-200 dark:border-white/10 shadow-md" />
                @else
                    <div class="w-24 h-24 rounded-full bg-[#d2e823] text-black flex items-center justify-center text-3xl font-black shadow-md">
                        {{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 1)) }}
                    </div>
                @endif

            </div>

            <!-- USER INFO -->
            <div>
                <p class="text-2xl font-bold text-slate-900 dark:text-white leading-tight">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ auth()->user()->email }}
                </p>

                <div class="flex gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-400">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

        </div>

        <!-- UPLOAD -->
        <div class="space-y-2 p-4 bg-slate-50 dark:bg-black/20 rounded-2xl border border-slate-100 dark:border-white/[0.03]">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Update Profile Picture</p>
            <div class="flex items-center gap-3 flex-wrap">
                <input type="file"
                    wire:model="photo"
                    class="text-xs text-slate-500 dark:text-slate-400 file:bg-white dark:file:bg-[#080c14] file:border file:border-slate-200 dark:file:border-white/10 file:px-4 file:py-2 file:rounded-xl file:font-semibold file:text-slate-800 dark:file:text-white hover:file:bg-slate-100 dark:hover:file:bg-white/5 file:cursor-pointer cursor-pointer" />

                <button
                    wire:click="savePhoto"
                    wire:loading.attr="disabled"
                    class="bg-[#d2e823] text-black px-5 py-2 rounded-xl text-xs font-bold hover:opacity-90 active:scale-[0.98] transition flex items-center gap-2 shadow-sm disabled:opacity-50">
                    <span wire:loading.remove wire:target="savePhoto">Upload Photo</span>
                    <span wire:loading wire:target="savePhoto">Saving...</span>
                </button>
            </div>

            @error('photo')
                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror

            @if (session('success'))
                <div class="bg-green-500/15 border border-green-500/20 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-xl text-sm font-medium mt-2">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <!-- DETAILS -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">

            <div class="bg-slate-50 dark:bg-[#080c14] p-4 rounded-2xl border border-slate-200/80 dark:border-white/5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Department</p>
                <p class="mt-1 font-bold text-slate-900 dark:text-white">
                    {{ auth()->user()->department->name ?? '—' }}
                </p>
            </div>

            <div class="bg-slate-50 dark:bg-[#080c14] p-4 rounded-2xl border border-slate-200/80 dark:border-white/5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Designation</p>
                <p class="mt-1 font-bold text-slate-900 dark:text-white">
                    Faculty Member
                </p>
            </div>

            <div class="bg-slate-50 dark:bg-[#080c14] p-4 rounded-2xl border border-slate-200/80 dark:border-white/5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Member Since</p>
                <p class="mt-1 font-bold text-slate-900 dark:text-white">
                    {{ auth()->user()->created_at->format('d M Y') }}
                </p>
            </div>

        </div>

        <!-- UPDATE FORM -->
        <form wire:submit="updateInfo" class="space-y-4 pt-6 border-t border-slate-100 dark:border-white/5">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Update Personal Information</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Full Name</label>
                    <input type="text" wire:model="name" class="w-full bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition" required>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Email Address</label>
                    <input type="email" wire:model="email" class="w-full bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-xs hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                    Save Changes
                </button>
            </div>
        </form>

        <!-- STATS -->
        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-white/5">

            <div class="bg-slate-50 dark:bg-[#080c14] p-5 rounded-2xl border border-slate-200/80 dark:border-white/5 text-center">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Dept Complaints</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $this->stats['total'] }}</p>
            </div>

            <div class="bg-slate-50 dark:bg-[#080c14] p-5 rounded-2xl border border-slate-200/80 dark:border-white/5 text-center">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Pending Attention</p>
                <p class="text-3xl font-extrabold text-amber-600 dark:text-yellow-400 mt-1">{{ $this->stats['pending'] }}</p>
            </div>

        </div>

    </div>

</div>
