<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.fullscreen')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PasswordReset) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: false);
    }
}; ?>

<div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-[#0c1018] relative overflow-hidden">
    {{-- Background accents --}}
    <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#d2e823]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        {{-- Brand Logo --}}
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
                <h2 class="font-display font-bold text-2xl text-white tracking-tight">Set new password</h2>
                <p class="text-sm text-slate-400 mt-1">Please enter your new password below.</p>
            </div>

            @if (session('status'))
                <div class="bg-[#d2e823]/15 border border-[#d2e823]/30 text-[#d2e823] px-4 py-3 rounded-2xl text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="resetPassword" class="space-y-4">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Email Address</label>
                    <input wire:model="email" id="email" type="email" required autocomplete="email"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#d2e823] focus:ring-1 focus:ring-[#d2e823] transition" />
                    @error('email')
                        <p class="text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">New Password</label>
                    <input wire:model="password" id="password" type="password" required autocomplete="new-password" placeholder="••••••••••"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#d2e823] focus:ring-1 focus:ring-[#d2e823] transition" />
                    @error('password')
                        <p class="text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Confirm Password</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password" placeholder="••••••••••"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#d2e823] focus:ring-1 focus:ring-[#d2e823] transition" />
                    @error('password_confirmation')
                        <p class="text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full mt-2 bg-[#d2e823] hover:bg-[#b8cf10] active:scale-[0.98] text-black font-display font-bold py-3.5 px-4 rounded-2xl text-sm transition shadow-lg shadow-[#d2e823]/20 flex items-center justify-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                    <span wire:loading wire:target="resetPassword">Updating password...</span>
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
