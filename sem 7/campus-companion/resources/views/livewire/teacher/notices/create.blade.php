<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Notice;
use App\Models\ClassRoom;
use App\Models\Notification;

return new #[Layout('components.layouts.teacher')] class extends Component {

    public $title = '';
    public $description = '';
    public $category = 'general';
    public $class_room_id = '';

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'category' => 'required',
            'class_room_id' => 'nullable|exists:class_rooms,id',
        ]);

        $user = auth()->user();

        $notice = Notice::create([
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'department_id' => $user->department_id,
            'class_room_id' => $this->class_room_id ?: null,
            'created_by' => $user->id,
        ]);

        // Find students in the target scope to send notifications
        $studentsQuery = \App\Models\User::where('role', 'student')
            ->where('department_id', $user->department_id);

        if ($this->class_room_id) {
            $studentsQuery->where('class_room_id', $this->class_room_id);
        }

        $students = $studentsQuery->get();

        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'message' => "New Notice: '{$this->title}' posted by {$user->name}",
            ]);
        }

        session()->flash('success', 'Notice created and students notified successfully');

        return redirect('/teacher/notices');
    }

    public function getClassroomsProperty()
    {
        $user = auth()->user();
        if (!$user->department_id) {
            return collect();
        }
        return ClassRoom::where('department_id', $user->department_id)->get();
    }
};
?>

<div class="space-y-8">

    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Create Notice</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Publish notices and automatically alert targeted students.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500/15 border border-green-500/20 text-green-600 dark:text-green-400 px-4 py-2.5 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 p-6 rounded-3xl space-y-5 max-w-2xl shadow-sm dark:shadow-none">

        <!-- TITLE -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Title</label>
            <input wire:model="title" placeholder="Notice Title"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition" />
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- DESCRIPTION -->
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Content / Details</label>
            <textarea wire:model="description" placeholder="Notice Details..." rows="6"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 focus:ring-1 focus:ring-lime-500/20 dark:focus:ring-[#d2e823]/20 transition resize-none"></textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <!-- CATEGORY -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Category</label>
                <select wire:model="category"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="general">General</option>
                    <option value="academic">Academic</option>
                    <option value="activity">Activity / Task</option>
                    <option value="administrative">Administrative</option>
                </select>
                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- CLASS SCOPE -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Target Class (Optional)</label>
                <select wire:model="class_room_id"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">All Classes in Department</option>
                    @foreach($this->classrooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
                @error('class_room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="button" wire:click="save"
                class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                Publish Notice
            </button>
            <a href="/teacher/notices"
                class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                Cancel
            </a>
        </div>

    </div>

</div>
