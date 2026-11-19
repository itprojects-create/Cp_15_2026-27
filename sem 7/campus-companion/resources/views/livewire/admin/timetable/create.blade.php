<?php

use App\Models\Timetable;
use App\Models\User;
use App\Models\ClassRoom;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {

    public $day = '';
    public $time_start = '';
    public $time_end = '';
    public $subject = '';
    public $faculty_id = '';

    #[Url]
    public $class_room_id = '';

    public $room = '';

    public $teachers = [];
    public $classes = [];

    public function mount()
    {
        $this->classes = ClassRoom::with('department')->get();

        if ($this->class_room_id) {
            $class = ClassRoom::find($this->class_room_id);
            if ($class) {
                $this->teachers = User::where('role', 'teacher')
                    ->where('department_id', $class->department_id)
                    ->get();
            } else {
                $this->teachers = User::where('role', 'teacher')->get();
            }
        } else {
            $this->teachers = User::where('role', 'teacher')->get();
        }
    }

    public function updatedClassRoomId($value)
    {
        if ($value) {
            $class = ClassRoom::find($value);
            if ($class) {
                $this->teachers = User::where('role', 'teacher')
                    ->where('department_id', $class->department_id)
                    ->get();
            } else {
                $this->teachers = [];
            }
        } else {
            $this->teachers = User::where('role', 'teacher')->get();
        }
        $this->faculty_id = ''; // Reset selected teacher when class changes
    }

    public function save()
    {
        $this->validate([
            'day' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'subject' => 'required',
            'faculty_id' => 'required|exists:users,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'room' => 'required',
        ]);

        $conflict = Timetable::hasConflict(
            $this->day,
            $this->time_start,
            $this->time_end,
            $this->class_room_id,
            $this->faculty_id
        );

        if ($conflict) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'conflict' => $conflict,
            ]);
        }

        $time_slot = Timetable::normalizeSlot($this->time_start . '-' . $this->time_end);

        Timetable::create([
            'day' => $this->day,
            'time_slot' => $time_slot,
            'subject' => $this->subject,
            'faculty_id' => $this->faculty_id,
            'class_room_id' => $this->class_room_id,
            'room' => $this->room,
        ]);

        return redirect('/admin/timetable');
    }
};
?>

<div class="max-w-2xl mx-auto space-y-8">

    <div>
        <a href="/admin/timetable" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white mb-3 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to Timetable
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Add Lecture</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Create a new timetable entry and avoid room/faculty clashes.</p>
    </div>

    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-6 shadow-sm dark:shadow-none">

        <form wire:submit="save" class="space-y-5">

            @error('conflict')
                <div class="p-3.5 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-2xl text-xs font-semibold">
                    {{ $message }}
                </div>
            @enderror

            <!-- Day -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Day of Week</label>
                <select wire:model="day" class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">Select Day</option>
                    <option>Monday</option>
                    <option>Tuesday</option>
                    <option>Wednesday</option>
                    <option>Thursday</option>
                    <option>Friday</option>
                </select>
                @error('day') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Time -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Start Time</label>
                    <input type="time" wire:model="time_start" class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    @error('time_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">End Time</label>
                    <input type="time" wire:model="time_end" class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    @error('time_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Subject -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Subject Name</label>
                <input type="text" wire:model="subject" placeholder="e.g. Distributed Systems"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Faculty -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Faculty / Teacher</label>
                <select wire:model="faculty_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">Select Teacher</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->email }})</option>
                    @endforeach
                </select>
                @error('faculty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Class -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Class</label>
                <select wire:model.live="class_room_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    <option value="">Select Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->year }} - {{ $c->name }} ({{ $c->department?->name }})
                        </option>
                    @endforeach
                </select>
                @error('class_room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Room -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Classroom / Lab Number</label>
                <input type="text" wire:model="room" placeholder="e.g. 402"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                @error('room') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                    Save Lecture
                </button>
                <a href="/admin/timetable" class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                    Cancel
                </a>
            </div>

        </form>

    </div>

</div>
