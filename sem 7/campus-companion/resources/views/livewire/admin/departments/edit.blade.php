<?php

use App\Models\Department;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {

    public $id;
    public $name;

    public function mount($id)
    {
        $dept = Department::findOrFail($id);

        $this->id = $dept->id;
        $this->name = $dept->name;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:departments,name,' . $this->id
        ]);

        Department::where('id', $this->id)->update([
            'name' => $this->name
        ]);

        session()->flash('success', 'Department updated successfully');

        return redirect('/admin/departments');
    }
};
?>

<div class="max-w-2xl mx-auto space-y-8">

    <div>
        <a href="/admin/departments" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white mb-3 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to Departments
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Department</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
            Update department name and properties.
        </p>
    </div>

    <div class="bg-white dark:bg-[#0f172a] p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-5 shadow-sm dark:shadow-none">

        <form wire:submit="update" class="space-y-5">

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Department Name</label>
                <input type="text" wire:model="name"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">

                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="bg-[#d2e823] text-black px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.98] transition shadow-md shadow-[#d2e823]/10">
                    Update Department
                </button>

                <a href="/admin/departments"
                   class="bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/5 px-6 py-2.5 rounded-xl font-bold text-sm hover:text-slate-900 dark:hover:text-white transition">
                    Cancel
                </a>
            </div>

        </form>

    </div>

</div>
