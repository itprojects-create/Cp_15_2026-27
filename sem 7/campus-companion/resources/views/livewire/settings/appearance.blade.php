<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex flex-col items-start w-full">
    @include('partials.settings-heading')

    <x-settings.layout heading="Appearance" subheading="Update your account's appearance settings">
        <div x-data="{
            currentTheme: localStorage.getItem('theme') || 'system',
            setTheme(val) {
                this.currentTheme = val;
                if (val === 'system') {
                    localStorage.removeItem('theme');
                    const isLight = window.matchMedia('(prefers-color-scheme: light)').matches;
                    document.documentElement.classList.toggle('light', isLight);
                    document.documentElement.classList.toggle('dark', !isLight);
                    document.documentElement.setAttribute('data-theme', isLight ? 'light' : 'dark');
                } else {
                    localStorage.setItem('theme', val);
                    document.documentElement.classList.toggle('light', val === 'light');
                    document.documentElement.classList.toggle('dark', val === 'dark');
                    document.documentElement.setAttribute('data-theme', val);
                }
            }
        }" class="space-y-4">
            <div class="grid grid-cols-3 gap-3 max-w-md">
                <button type="button" @click="setTheme('light')"
                    class="p-4 rounded-2xl border text-center transition-all flex flex-col items-center gap-2 cursor-pointer"
                    :class="currentTheme === 'light' ? 'bg-[#d2e823] text-black border-[#d2e823] font-bold shadow-md' : 'bg-white dark:bg-[#0f172a] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-slate-300'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <span class="text-xs">Light</span>
                </button>

                <button type="button" @click="setTheme('dark')"
                    class="p-4 rounded-2xl border text-center transition-all flex flex-col items-center gap-2 cursor-pointer"
                    :class="currentTheme === 'dark' ? 'bg-[#d2e823] text-black border-[#d2e823] font-bold shadow-md' : 'bg-white dark:bg-[#0f172a] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-slate-300'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    <span class="text-xs">Dark</span>
                </button>

                <button type="button" @click="setTheme('system')"
                    class="p-4 rounded-2xl border text-center transition-all flex flex-col items-center gap-2 cursor-pointer"
                    :class="currentTheme === 'system' ? 'bg-[#d2e823] text-black border-[#d2e823] font-bold shadow-md' : 'bg-white dark:bg-[#0f172a] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-slate-300'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="21" x2="16" y2="21" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="12" y1="17" x2="12" y2="21" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-xs">System</span>
                </button>
            </div>
        </div>
    </x-settings.layout>
</div>
