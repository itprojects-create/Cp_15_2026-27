<?php

use App\Models\Department;
use App\Services\TimetableImportService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;

    public $departments;
    public $selectedDepartment = null;
    public $file;
    public $previewData = null;
    public $clearExisting = true;
    public $showConfirmModal = false;
    public $importSuccess = false;
    public $importResult = null;
    public $errorMessage = null;

    public function mount()
    {
        $this->departments = Department::all();
        if ($this->departments->isNotEmpty()) {
            $this->selectedDepartment = $this->departments->first()->id;
        }
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|file|max:10240', // 10MB
            'selectedDepartment' => 'required|exists:departments,id',
        ]);

        $this->resetPreview();

        try {
            $path = $this->file->getRealPath();
            $service = app(TimetableImportService::class);
            $this->previewData = $service->parseWorkbook($path, (int) $this->selectedDepartment);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->previewData = null;
        }
    }

    public function resetPreview()
    {
        $this->previewData = null;
        $this->errorMessage = null;
        $this->importSuccess = false;
        $this->importResult = null;
        $this->showConfirmModal = false;
    }

    public function promptImport()
    {
        if (!$this->previewData || empty($this->previewData['lectures'])) {
            $this->errorMessage = 'No valid lectures found to import.';
            return;
        }

        $this->showConfirmModal = true;
    }

    public function cancelImport()
    {
        $this->showConfirmModal = false;
    }

    public function executeImport()
    {
        if (!$this->previewData || empty($this->previewData['lectures'])) {
            $this->errorMessage = 'No preview data available to import.';
            return;
        }

        try {
            $service = app(TimetableImportService::class);
            $this->importResult = $service->importLectures(
                (int) $this->selectedDepartment,
                $this->previewData['lectures'],
                $this->clearExisting
            );

            $this->importSuccess = true;
            $this->showConfirmModal = false;
            $this->previewData = null;
            $this->file = null;
        } catch (\Exception $e) {
            $this->errorMessage = 'Import failed: ' . $e->getMessage();
            $this->showConfirmModal = false;
        }
    }

    public function downloadTemplate()
    {
        $service = app(TimetableImportService::class);
        $csv = $service->generateCsvTemplate();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'master_timetable_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
};
?>

<div class="space-y-8 max-w-7xl mx-auto pb-16">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="/admin/timetable" wire:navigate class="text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Timetable
                </a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs font-semibold text-blue-600 dark:text-[#d2e823]">Excel Import</span>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Master Timetable Excel Import</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Upload a departmental Master Excel sheet to automatically generate all class and faculty timetables.</p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="downloadTemplate" class="px-4 py-2.5 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download Template
            </button>
            <a href="/admin/timetable/master" wire:navigate class="px-4 py-2.5 rounded-xl bg-[#d2e823] hover:opacity-90 active:scale-[0.98] text-black text-xs font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                View Master Grid
            </a>
        </div>
    </div>

    <!-- Error Banner -->
    @if ($errorMessage)
        <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-2xl text-sm font-semibold flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', null)" class="text-xs hover:underline">Dismiss</button>
        </div>
    @endif

    <!-- Success Banner -->
    @if ($importSuccess && $importResult)
        <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-emerald-800 dark:text-emerald-300">Timetable Successfully Generated!</h3>
                    <p class="text-xs text-emerald-700 dark:text-emerald-400">
                        Imported <strong class="font-bold">{{ $importResult['total_inserted'] }}</strong> lectures across <strong class="font-bold">{{ $importResult['total_classes'] }}</strong> classes for department <span class="font-bold text-slate-900 dark:text-white">{{ $importResult['department_name'] }}</span>.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <a href="/admin/timetable/master?department_id={{ $selectedDepartment }}" wire:navigate class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm">
                    Open Department Master Grid
                </a>
                <a href="/admin/timetable" wire:navigate class="px-4 py-2 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 dark:hover:bg-white/10 transition">
                    View Class Timetables
                </a>
                <button wire:click="resetPreview" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 text-xs font-bold hover:text-slate-900 dark:hover:text-white transition">
                    Import Another File
                </button>
            </div>
        </div>
    @endif

    <!-- Upload Form Card -->
    <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-6 sm:p-8 shadow-sm dark:shadow-none space-y-6">
        <div class="grid md:grid-cols-2 gap-6">

            <!-- Department Selection -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">1. Select Target Department</label>
                <select wire:model.live="selectedDepartment" class="w-full px-4 py-3 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/8 rounded-2xl text-sm text-slate-900 dark:text-white font-medium outline-none focus:border-lime-500 dark:focus:border-[#d2e823]/40 transition">
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400">All classes & lectures in this Excel will be organized under this department.</p>
            </div>

            <!-- Overwrite Option -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">2. Import Mode</label>
                <div class="p-3 bg-slate-50 dark:bg-black/20 border border-slate-200/80 dark:border-white/5 rounded-2xl flex items-start gap-3">
                    <input type="checkbox" id="clearExisting" wire:model="clearExisting" class="mt-1 w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                    <label for="clearExisting" class="cursor-pointer text-xs text-slate-700 dark:text-slate-300">
                        <strong class="font-semibold text-slate-900 dark:text-white block">Replace existing department timetable (Recommended)</strong>
                        Clears previous timetable slots for this department to avoid duplicate slots or stale schedules.
                    </label>
                </div>
            </div>

        </div>

        <!-- Dropzone / File Upload -->
        <div class="space-y-2">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">3. Upload Master Timetable Excel Sheet (.xlsx / .csv)</label>

            <div class="relative border-2 border-dashed border-slate-200 dark:border-white/10 hover:border-lime-500 dark:hover:border-[#d2e823]/50 transition rounded-3xl p-8 text-center bg-slate-50/50 dark:bg-black/20 group">
                <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                <div wire:loading.remove wire:target="file" class="flex flex-col items-center justify-center space-y-3 pointer-events-none">
                    <div class="w-14 h-14 rounded-2xl bg-lime-500/10 text-lime-600 dark:text-[#d2e823] flex items-center justify-center group-hover:scale-110 transition duration-200">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            @if ($file)
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Selected: {{ $file->getClientOriginalName() }}</span>
                            @else
                                Click or drag and drop your Master Timetable Excel file here
                            @endif
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Accepts Excel (.xlsx, .xls) containing Department Master Timetable + optional Faculty Mapping sheet.</p>
                    </div>
                </div>

                <!-- Loading State -->
                <div wire:loading wire:target="file" class="flex flex-col items-center justify-center space-y-3">
                    <div class="w-10 h-10 border-3 border-lime-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Parsing workbook and mapping faculty initials...</p>
                </div>
            </div>
            @error('file') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- PREVIEW SECTION -->
    @if ($previewData)
        <div class="space-y-6 pt-2">

            <!-- Summary Stats Card -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Lectures</span>
                    <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $previewData['total_lectures'] }}</p>
                </div>
                <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Detected Classes</span>
                    <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $previewData['total_classes'] }}</p>
                </div>
                <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Days Recognized</span>
                    <p class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ count($previewData['days']) }}</p>
                </div>
                <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-2xl p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Faculty Mapped</span>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ count($previewData['mapped_faculty']) }}</p>
                </div>
            </div>

            <!-- Detected Classes & Faculty Summary -->
            <div class="grid md:grid-cols-2 gap-4">
                <!-- Classes List -->
                <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Classes In This Import</h3>
                    <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto pr-1">
                        @foreach ($previewData['classes'] as $cls)
                            <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-300 text-xs font-bold">
                                {{ $cls['name'] }} @if($cls['room']) <span class="opacity-70 font-normal text-[10px]">(Rm {{ $cls['room'] }})</span> @endif
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Faculty Mapping summary -->
                <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Faculty Initials Matched</h3>
                    <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto pr-1">
                        @forelse ($previewData['mapped_faculty'] as $initial => $fName)
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-medium" title="{{ $fName }}">
                                <strong>{{ $initial }}</strong> &rarr; {{ Str::limit($fName, 18) }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-400">No initials mapped</span>
                        @endforelse
                    </div>
                </div>
            </div>

            @if (!empty($previewData['unmapped_initials']))
                <div class="bg-amber-500/10 border border-amber-500/25 rounded-3xl p-5 space-y-2">
                    <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-bold text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span>{{ count($previewData['unmapped_initials']) }} Unmapped Faculty Initials Detected</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400">
                        The following initials were found in lecture slots but could not be matched to a registered teacher in this department. They will be imported without an assigned teacher:
                    </p>
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach ($previewData['unmapped_initials'] as $initial => $count)
                            <span class="px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs font-semibold">
                                {{ $initial }} <span class="opacity-75">({{ $count }} {{ Str::plural('lecture', $count) }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Parsed Lectures Sample Table -->
            <div class="bg-white dark:bg-[#0f172a]/70 border border-slate-200 dark:border-white/5 rounded-3xl overflow-hidden shadow-sm dark:shadow-none space-y-3 p-5">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Lectures Preview (First 20 of {{ $previewData['total_lectures'] }})</h3>
                    <span class="text-xs text-slate-500">Days: {{ implode(', ', $previewData['days']) }}</span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-white/5">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 dark:bg-[#0b1120] text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-white/10">
                            <tr>
                                <th class="p-3">Day</th>
                                <th class="p-3">Time Slot</th>
                                <th class="p-3">Class</th>
                                <th class="p-3">Subject</th>
                                <th class="p-3">Faculty / Initials</th>
                                <th class="p-3">Room</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                            @foreach (array_slice($previewData['lectures'], 0, 20) as $lec)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02]">
                                    <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ $lec['day'] }}</td>
                                    <td class="p-3 font-mono text-slate-600 dark:text-slate-400">{{ $lec['time_slot'] }}</td>
                                    <td class="p-3 font-bold text-blue-600 dark:text-blue-400">{{ $lec['class_name'] }}</td>
                                    <td class="p-3">
                                        @if ($lec['is_break'])
                                            <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-yellow-400 font-bold text-[10px]">BREAK</span>
                                        @else
                                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $lec['subject'] }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if ($lec['faculty_name'])
                                            <span class="text-slate-700 dark:text-slate-300">{{ $lec['faculty_name'] }}</span>
                                            @if($lec['faculty_initials'])
                                                <span class="text-[10px] text-slate-400 font-mono">({{ $lec['faculty_initials'] }})</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-mono font-bold text-slate-600 dark:text-slate-400">{{ $lec['room'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="resetPreview" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 text-xs font-bold hover:text-slate-900 dark:hover:text-white transition">
                    Discard Preview
                </button>
                <button wire:click="promptImport" class="px-6 py-2.5 rounded-xl bg-[#d2e823] hover:opacity-90 active:scale-[0.98] text-black text-xs font-bold transition shadow-md shadow-[#d2e823]/10 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    Confirm & Generate Department Timetable
                </button>
            </div>

        </div>
    @endif

    <!-- Overwrite Confirmation Modal -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-yellow-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div class="text-center space-y-2">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Confirm Timetable Generation</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        @if ($clearExisting)
                            This will <strong class="text-red-500">replace all existing timetable entries</strong> for the selected department and populate <strong class="text-slate-800 dark:text-white">{{ $previewData['total_lectures'] ?? 0 }}</strong> new lectures.
                        @else
                            This will add <strong class="text-slate-800 dark:text-white">{{ $previewData['total_lectures'] ?? 0 }}</strong> new lectures to the selected department timetable.
                        @endif
                    </p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="cancelImport" class="flex-1 py-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-white/10 transition">
                        Cancel
                    </button>
                    <button wire:click="executeImport" class="flex-1 py-2.5 rounded-xl bg-[#d2e823] hover:opacity-90 active:scale-[0.98] text-black text-xs font-bold transition shadow-sm">
                        Proceed with Import
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
