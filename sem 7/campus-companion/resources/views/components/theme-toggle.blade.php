<div x-data="{
    theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'),
    toggle() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', this.theme);
        this.apply();
    },
    apply() {
        if (this.theme === 'light') {
            document.documentElement.classList.add('light');
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }
}" x-init="apply()" class="relative flex items-center">
    <button @click="toggle()"
        type="button"
        title="Toggle Theme"
        class="theme-toggle-btn w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 border cursor-pointer"
        :class="theme === 'dark' ? 'bg-white/5 hover:bg-white/10 text-yellow-400 border-white/10' : 'bg-black/5 hover:bg-black/10 text-slate-700 border-black/10'">
        
        <!-- Sun icon (shown when dark mode, click to switch to light) -->
        <svg x-show="theme === 'dark'" x-cloak class="w-4 h-4 transition-transform duration-200 hover:rotate-45" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
        </svg>

        <!-- Moon icon (shown when light mode, click to switch to dark) -->
        <svg x-show="theme === 'light'" x-cloak class="w-4 h-4 transition-transform duration-200 hover:-rotate-12" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
        </svg>
    </button>
</div>
