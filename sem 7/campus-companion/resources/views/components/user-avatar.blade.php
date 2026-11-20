@props([
    'user' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $targetUser = $user ?? auth()->user();
    $name = $targetUser?->name ?? 'User';
    $initials = strtoupper(substr($name, 0, 1));
    $photo = $targetUser?->profile_photo;

    $sizeClasses = match($size) {
        'xs' => 'w-6 h-6 text-[0.65rem]',
        'sm' => 'w-8 h-8 text-xs',
        'lg' => 'w-12 h-12 text-base font-bold',
        'xl' => 'w-16 h-16 text-xl font-extrabold',
        default => 'w-10 h-10 text-sm font-bold',
    };
@endphp

@if($photo)
    <img
        src="{{ asset('storage/' . $photo) }}"
        alt="{{ $name }}"
        class="{{ $sizeClasses }} rounded-full object-cover ring-1 ring-black/10 dark:ring-white/10 shrink-0 {{ $class }}"
        onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
    />
    <div
        style="display: none;"
        class="{{ $sizeClasses }} rounded-full bg-gradient-to-br from-[#d2e823] to-[#a3b814] text-black font-display items-center justify-center font-bold tracking-tight ring-1 ring-black/10 dark:ring-white/10 shadow-sm shrink-0 {{ $class }}"
        title="{{ $name }}">
        {{ $initials }}
    </div>
@else
    <div
        class="{{ $sizeClasses }} rounded-full bg-gradient-to-br from-[#d2e823] to-[#a3b814] text-black font-display flex items-center justify-center font-bold tracking-tight ring-1 ring-black/10 dark:ring-white/10 shadow-sm shrink-0 {{ $class }}"
        title="{{ $name }}">
        {{ $initials }}
    </div>
@endif
