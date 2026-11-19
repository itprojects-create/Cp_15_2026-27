<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Notice;

return new #[Layout('components.layouts.admin')] class extends Component {

    public $notice;
    public $title;
    public $description;
    public $category;
    public $department_id = '';
    public $class_room_id = '';

    public function mount($id)
    {
        $this->notice = Notice::findOrFail($id);

        $this->title = $this->notice->title;
        $this->description = $this->notice->description;
        $this->category = $this->notice->category;
        $this->department_id = $this->notice->department_id ?? '';
        $this->class_room_id = $this->notice->class_room_id ?? '';
    }

    public function updatedDepartmentId()
    {
        $this->class_room_id = '';
    }

    public function getDepartmentsProperty()
    {
        return \App\Models\Department::orderBy('name')->get();
    }

    public function getClassroomsProperty()
    {
        if ($this->department_id) {
            return \App\Models\ClassRoom::where('department_id', $this->department_id)->orderBy('year')->orderBy('name')->get();
        }
        return \App\Models\ClassRoom::orderBy('year')->orderBy('name')->get();
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'category' => 'required',
            'department_id' => 'nullable|exists:departments,id',
            'class_room_id' => 'nullable|exists:class_rooms,id',
        ]);

        $this->notice->update([
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'department_id' => $this->department_id ?: null,
            'class_room_id' => $this->class_room_id ?: null,
        ]);

        return redirect('/admin/notices');
    }
};
?>

<div class="space-y-8 max-w-2xl">

    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Notice</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Update notice details and category.</p>
    </div>

    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 p-6 rounded-3xl space-y-5 shadow-sm dark:shadow-none">

        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Title</label>
            <input wire:model="title"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition" />
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Description</label>
            <textarea wire:model="description"
                rows="5"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition resize-none"></textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Category</label>
            <select wire:model="category"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                <option value="general">General</option>
                <option value="academic">Academic</option>
                <option value="administrative">Administrative</option>
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Target Department</label>
                <select wire:model.live="department_id"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">All Departments (Global Campus)</option>
                    @foreach($this->departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Target Class (Optional)</label>
                <select wire:model="class_room_id"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">All Classes in Scope</option>
                    @foreach($this->classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->year }} {{ $cls->name }} ({{ $cls->department?->name ?? 'General' }})</option>
                    @endforeach
                </select>
                @error('class_room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 flex gap-3">
            <button wire:click="update"
                wire:loading.attr="disabled"
                class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] disabled:opacity-50 inline-flex items-center gap-2 transition shadow-md shadow-[#d2e823]/10">
                <span wire:loading.remove wire:target="update">Update Notice</span>
                <span wire:loading wire:target="update" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                </span>
            </button>

            <a href="/admin/notices"
                class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                Cancel
            </a>
        </div>

    </div>

</div>
