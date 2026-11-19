<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Department;
use App\Models\ClassRoom;

return new #[Layout('components.layouts.admin')] class extends Component {

    public $search = '';

    public $roleFilter = '';
    public $departmentFilter = '';
    public $classFilter = '';

    public $applied = false;

    public $departments = [];
    public $classes = [];

    public function mount()
    {
        $this->departments = Department::all();
    }

    // load classes when department changes
    public function updated($field)
    {
        if ($field === 'departmentFilter') {
            $this->classes = ClassRoom::where('department_id', $this->departmentFilter)->get();
            $this->classFilter = '';
        }
    }

    public function applyFilters()
    {
        $this->applied = true;
    }

    public function clearFilters()
    {
        $this->reset(['roleFilter', 'departmentFilter', 'classFilter', 'search']);
        $this->classes = [];
        $this->applied = false;
    }

    public function getUsersProperty()
    {
        $query = User::with(['department', 'classRoom']);

        // search always active
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // filters only when applied
        if ($this->applied) {
            $query->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
                  ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
                  ->when($this->classFilter, fn($q) => $q->where('class_room_id', $this->classFilter));
        }

        return $query->latest()->get();
    }

    public function delete($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user = User::findOrFail($id);

        $lecturesCount = $user->timetables()->count();
        if ($lecturesCount > 0) {
            session()->flash('error', "Cannot delete faculty '{$user->name}': They are assigned to {$lecturesCount} active timetable lecture(s). Reassign their lectures before deleting.");
            return;
        }

        $user->delete();

        session()->flash('success', 'User deleted successfully');
    }
};
?>

<div class="space-y-8">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Users</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                Manage students, teachers, and admins across departments
            </p>
        </div>

        <a href="/admin/users/create"
           class="bg-[#d2e823] text-black px-5 py-2.5 rounded-xl text-sm font-bold hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
            + Add User
        </a>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="bg-green-500/15 border border-green-500/20 text-green-700 dark:text-green-400 px-4 py-2.5 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- ERROR -->
    @if(session('error'))
        <div class="bg-rose-500/15 border border-rose-500/20 text-rose-700 dark:text-rose-400 px-4 py-2.5 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- SEARCH + FILTERS -->
    <div class="bg-white dark:bg-[#0f172a] p-5 rounded-3xl border border-slate-200 dark:border-white/5 space-y-4 shadow-sm dark:shadow-none">

        <!-- SEARCH -->
        <input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name or email..."
            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition"
        />

        <!-- FILTERS -->
        <div class="grid sm:grid-cols-3 gap-3">

            <!-- Role -->
            <select wire:model.live="roleFilter"
                class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>

            <!-- Department -->
            <select wire:model.live="departmentFilter"
                class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            <!-- Class -->
            <select wire:model.live="classFilter"
                class="px-4 py-2.5 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->year }} - {{ $class->name }}
                    </option>
                @endforeach
            </select>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex gap-2 pt-1">

            <button wire:click="applyFilters"
                class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-xl text-xs transition shadow-sm">
                Apply Filters
            </button>

            <button wire:click="clearFilters"
                class="bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/15 text-slate-700 dark:text-slate-300 font-semibold px-4 py-2 rounded-xl text-xs transition">
                Clear
            </button>

        </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-[#0f172a] rounded-3xl border border-slate-200 dark:border-white/5 overflow-x-auto shadow-sm dark:shadow-none">

        <table class="w-full text-sm">

            <thead class="bg-slate-50 dark:bg-[#080c14] text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-white/5">
                <tr>
                    <th class="p-4 text-left text-xs uppercase tracking-wider">Name</th>
                    <th class="p-4 text-left text-xs uppercase tracking-wider">Email</th>
                    <th class="p-4 text-left text-xs uppercase tracking-wider">Role</th>
                    <th class="p-4 text-left text-xs uppercase tracking-wider">Department</th>
                    <th class="p-4 text-left text-xs uppercase tracking-wider">Class</th>
                    <th class="p-4 text-left text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 dark:divide-white/5">

                @forelse($this->users as $user)

                    <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.03] transition">

                        <td class="p-4 font-bold text-slate-900 dark:text-white">
                            {{ $user->name }}
                        </td>

                        <td class="p-4 text-slate-600 dark:text-slate-300">
                            {{ $user->email }}
                        </td>

                        <td class="p-4">
                            <span class="px-2.5 py-1 text-xs rounded-full font-semibold
                                @if($user->role === 'admin') bg-purple-500/15 text-purple-700 dark:text-purple-400
                                @elseif($user->role === 'teacher') bg-blue-500/15 text-blue-700 dark:text-blue-400
                                @else bg-green-500/15 text-green-700 dark:text-green-400
                                @endif
                            ">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <!-- Department -->
                        <td class="p-4 text-slate-600 dark:text-slate-300">
                            {{ optional($user->department)->name ?? '—' }}
                        </td>

                        <!-- Class -->
                        <td class="p-4 text-slate-600 dark:text-slate-300">
                            @if($user->classRoom)
                                {{ $user->classRoom->year }} - {{ $user->classRoom->name }}
                            @else
                                —
                            @endif
                        </td>

                        <td class="p-4">
                            <div class="flex gap-3 items-center">
                                <a href="/admin/users/edit/{{ $user->id }}"
                                    class="text-blue-600 dark:text-blue-400 text-xs font-bold hover:underline">
                                    Edit
                                </a>

                                @if($user->id !== auth()->id())
                                    <button
                                        wire:click="delete({{ $user->id }})"
                                        wire:confirm="Are you sure you want to delete this user?"
                                        class="text-red-500 dark:text-red-400 text-xs font-bold hover:underline">
                                        Delete
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 italic">
                                        (You)
                                    </span>
                                @endif
                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-slate-400 dark:text-slate-500">
                            No users found
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
