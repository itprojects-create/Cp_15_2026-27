<!-- Report Clash Modal Component -->
@if($showClashModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-3xl p-6 w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto space-y-6">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-white/5">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white font-display">Report Timetable Clash</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Find available alternative classrooms for a specific day and time slot.</p>
                </div>
                <button type="button" wire:click="closeClashModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Input Form -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Day</label>
                    <select wire:model="clashDay" class="w-full bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-slate-900 dark:text-white text-sm outline-none focus:border-lime-500 dark:focus:border-[#d2e823]">
                        <option value="">Select Day</option>
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                    @error('clashDay') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Time Slot</label>
                    <select wire:model="clashTimeSlot" class="w-full bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-slate-900 dark:text-white text-sm outline-none focus:border-lime-500 dark:focus:border-[#d2e823]">
                        <option value="">Select Time</option>
                        @foreach(['08:30-09:30', '09:30-10:30', '10:30-11:30', '11:30-12:30', '12:30-01:30', '01:30-02:30', '02:30-03:30'] as $slot)
                            <option value="{{ $slot }}">{{ $slot }}</option>
                        @endforeach
                    </select>
                    @error('clashTimeSlot') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Clashing Class</label>
                    <select wire:model="clashingClassId" class="w-full bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/10 rounded-xl p-2.5 text-slate-900 dark:text-white text-sm outline-none focus:border-lime-500 dark:focus:border-[#d2e823]">
                        <option value="">Select Class</option>
                        @foreach(\App\Models\ClassRoom::with('department')->orderBy('year')->orderBy('name')->get() as $room)
                            <option value="{{ $room->id }}">{{ $room->year }} {{ $room->name }} ({{ $room->department?->name }})</option>
                        @endforeach
                    </select>
                    @error('clashingClassId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" wire:click="findClashAlternatives" class="bg-[#d2e823] hover:opacity-90 active:scale-[0.98] px-5 py-2.5 rounded-xl text-black font-bold text-xs transition shadow-lg shadow-[#d2e823]/10 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Find Available Rooms
                </button>
            </div>

            <!-- Results Section -->
            @if($clashSearched)
                <div class="border-t border-slate-100 dark:border-white/5 pt-5 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white font-display">Available Free Classrooms</h3>
                    
                    @if(count($clashAlternatives) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-h-60 overflow-y-auto pr-1">
                            @foreach($clashAlternatives as $room)
                                <div class="bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/5 rounded-2xl p-4 flex flex-col gap-1 relative overflow-hidden shadow-xs">
                                    @if(isset($room['is_same_dept']) && $room['is_same_dept'])
                                        <div class="absolute top-0 right-0 bg-indigo-500/15 text-indigo-700 dark:text-indigo-400 text-[10px] font-bold px-2 py-0.5 rounded-bl-xl border-b border-l border-indigo-500/20">
                                            Same Dept
                                        </div>
                                    @endif

                                    <div class="font-bold text-slate-900 dark:text-white text-base">
                                        {{ $room['year'] }} - {{ $room['name'] }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        Dept: <span class="text-slate-800 dark:text-slate-300 font-medium">{{ $room['department_name'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        Location: <span class="text-slate-800 dark:text-slate-300 font-medium">{{ $room['floor_name'] ?? 'Floor Not Mapped' }} {{ !empty($room['block_number']) ? '(Block ' . $room['block_number'] . ')' : '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-500 text-sm bg-slate-50 dark:bg-[#080c14] border border-slate-200 dark:border-white/5 rounded-2xl">
                            No free classrooms available for this time slot.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif
