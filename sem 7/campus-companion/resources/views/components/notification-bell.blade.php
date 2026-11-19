<?php

use App\Models\Notification;

$notifications = Notification::where('user_id', auth()->id())
    ->latest()
    ->take(5)
    ->get();

$unread = Notification::where('user_id', auth()->id())
    ->where('read', false)
    ->count();
?>

<div x-data="{
    open: false,
    unread: {{ $unread }},
    markAllRead() {
        if (this.unread > 0) {
            fetch('{{ route('notifications.mark-as-read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (res.ok) {
                    this.unread = 0;
                }
            }).catch(() => {});
        }
    }
}" class="relative">

    <!-- ICON -->
    <button @click="open = !open; if(open) markAllRead();" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        <span x-show="unread > 0" x-text="unread" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[0.65rem] font-bold flex items-center justify-center rounded-full"></span>
    </button>

    <!-- DROPDOWN -->
    <div x-show="open" @click.outside="open=false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-white/10 rounded-2xl p-3 shadow-xl dark:shadow-2xl z-50"
        x-cloak>

        <div class="px-2 py-1.5 mb-2 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Notifications</span>
            <div class="flex items-center gap-2">
                <span x-show="unread > 0" x-text="unread + ' new'" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-500/10 text-red-500"></span>
                <button type="button" @click="markAllRead()" class="text-[10px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 underline">
                    Mark all read
                </button>
            </div>
        </div>

        @if($notifications->isEmpty())
            <div class="py-6 text-center text-slate-400 dark:text-slate-500 text-xs">
                No notifications
            </div>
        @else
            <div class="space-y-1">
                @foreach($notifications as $n)
                    <div class="p-2.5 rounded-xl text-xs transition {{ !$n->read ? 'bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                        {{ $n->message }}
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>
