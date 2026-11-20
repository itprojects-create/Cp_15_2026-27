<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.fullscreen')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        try {
            Password::sendResetLink($this->only('email'));
            session()->flash('status', __('A reset link will be sent if the account exists.'));
        } catch (\Throwable $e) {
            session()->flash('status', __('A reset link will be sent if the account exists.'));
        }
    }
}; ?>

<div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-[#0c1018] relative overflow-hidden">
    {{-- Background accents --}}
    <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#d2e823]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        {{-- Brand Logo / Return --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-[#d2e823] flex items-center justify-center shadow-lg shadow-[#d2e823]/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <span class="font-display font-extrabold text-2xl tracking-tight text-white">Campus Companion</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-[#111820]/90 backdrop-blur-xl border border-white/10 rounded-3xl p-7 sm:p-9 shadow-2xl space-y-6">
            <div>
                <h2 class="font-display font-bold text-2xl text-white tracking-tight">Reset password</h2>
                <p class="text-sm text-slate-400 mt-1">Enter your registered email address to receive a secure reset link.</p>
            </div>

            @if (session('status'))
                <div class="bg-[#d2e823]/15 border border-[#d2e823]/30 text-[#d2e823] px-4 py-3 rounded-2xl text-sm font-medium flex items-center gap-2.5">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form wire:submit="sendPasswordResetLink" class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
                    <div class="relative">
                        <input wire:model="email" id="email" type="email" required autofocus
                            placeholder="you@vesit.edu.in"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#d2e823] focus:ring-1 focus:ring-[#d2e823] transition" />
                    </div>
                    @error('email')
                        <p class="text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-[#d2e823] hover:bg-[#b8cf10] active:scale-[0.98] text-black font-display font-bold py-3.5 px-4 rounded-2xl text-sm transition shadow-lg shadow-[#d2e823]/20 flex items-center justify-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="sendPasswordResetLink">Send Reset Link</span>
                    <span wire:loading wire:target="sendPasswordResetLink">Sending link...</span>
                </button>
            </form>

            <div class="pt-4 border-t border-white/5 text-center">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-white transition inline-flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5m7 7l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>
</div>
