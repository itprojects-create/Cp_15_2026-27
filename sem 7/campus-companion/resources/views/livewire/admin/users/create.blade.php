<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Jobs\SendUserCreatedMail;

return new #[Layout('components.layouts.admin')] class extends Component {
    public $name = '';
    public $email = '';
    public $role = 'student';

    public $department_id = '';
    public $class_room_id = '';

    public $departments = [];
    public $classes = [];

    public function mount()
    {
        $this->departments = Department::all();
    }

    // reactive class loading
    public function updated($field)
    {
        if ($field === 'department_id') {
            $this->classes = ClassRoom::where('department_id', $this->department_id)->get();
            $this->class_room_id = '';
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',

            'department_id' => !in_array($this->role, ['admin', 'timetable_commity']) ? 'required' : 'nullable',

            'class_room_id' => $this->role === 'student' ? 'required|exists:class_rooms,id' : 'nullable',
        ]);

        $password = Str::random(8);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => Hash::make($password),

            'department_id' => !in_array($this->role, ['admin', 'timetable_commity']) ? $this->department_id : null,
            'class_room_id' => $this->role === 'student' ? $this->class_room_id : null,
        ]);

        SendUserCreatedMail::dispatch($user, $password);

        session()->flash('success', 'User created & credentials emailed');

        return redirect('/admin/users');
    }
};
?>

<div class="max-w-2xl mx-auto space-y-8">

    <!-- HEADER -->
    <div>
        <a href="/admin/users" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white mb-3 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to Users
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Add User</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Create a new student, teacher, or administrative account.
        </p>
    </div>

    <!-- SUCCESS -->
    @if (session('success'))
        <div class="bg-green-500/15 border border-green-500/20 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- FORM -->
    <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-5 shadow-sm dark:shadow-none">

        <!-- NAME -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Full Name</label>
            <input wire:model.live="name" placeholder="e.g. Rahul Sharma"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition" />
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- EMAIL -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Email Address</label>
            <input wire:model.live="email" type="email" placeholder="e.g. rahul@ves.ac.in"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition" />
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- ROLE -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Role</label>
            <select wire:model.live="role"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="timetable_commity">Timetable Committee</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <!-- DEPARTMENT -->
        @if (!in_array($role, ['admin', 'timetable_commity']))
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Department</label>
                <select wire:model.live="department_id"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">Select Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <!-- CLASS (ONLY STUDENT) -->
        @if ($role === 'student')
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Class</label>
                <select wire:model.live="class_room_id"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">Select Class</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->year }} - {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_room_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="pt-4 flex gap-3">
            <button wire:click="save"
                wire:loading.attr="disabled"
                class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] disabled:opacity-50 inline-flex items-center gap-2 transition shadow-md shadow-[#d2e823]/10">
                <span wire:loading.remove wire:target="save">Create User</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                </span>
            </button>

            <a href="/admin/users"
                class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                Cancel
            </a>
        </div>

    </div>

</div>
