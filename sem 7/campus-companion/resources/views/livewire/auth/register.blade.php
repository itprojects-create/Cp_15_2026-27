<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.fullscreen')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    :root {
        --amber:    #f59e0b;
        --amber-lt: #fbbf24;
        --amber-dk: #d97706;
        --bg:       #080c14;
        --surface:  rgba(15, 23, 42, 0.72);
        --border:   rgba(245, 158, 11, 0.18);
        --text:     #f1f5f9;
        --muted:    #64748b;
        --error:    #f87171;
        --success:  #34d399;
    }

    html.light, [data-theme="light"] {
        --bg:       #f8fafc;
        --surface:  #ffffff;
        --border:   #e2e8f0;
        --text:     #0f172a;
        --muted:    #64748b;
    }

    html.light body {
        background: #f8fafc !important;
    }

    html.light .auth-left {
        background: linear-gradient(145deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        border-right-color: #e2e8f0 !important;
    }

    html.light .auth-left h2 {
        color: #0f172a !important;
    }

    html.light .auth-right {
        background: #f8fafc !important;
    }

    html.light .register-card {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06) !important;
    }

    html.light .field-input {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    html.light .field-input:focus {
        border-color: var(--amber) !important;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.18) !important;
    }

    html.light .field-label {
        color: #475569 !important;
    }

    * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    body {
        background: var(--bg);
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* ── CANVAS ──────────────────────────────────── */
    #bg-canvas {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
    }

    /* ── CARD ────────────────────────────────────── */
    .register-card {
        position: relative;
        z-index: 10;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 2.75rem 2.5rem;
        backdrop-filter: blur(28px) saturate(160%);
        -webkit-backdrop-filter: blur(28px) saturate(160%);
        box-shadow:
            0 0 0 1px rgba(245,158,11,0.06),
            0 32px 80px rgba(0,0,0,0.55),
            inset 0 1px 0 rgba(255,255,255,0.06);
        animation: cardIn 0.7s cubic-bezier(0.16,1,0.3,1) both;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(28px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0)    scale(1);    }
    }

    /* ── LOGO BADGE ──────────────────────────────── */
    .logo-badge {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, var(--amber-lt), var(--amber-dk));
        border-radius: 14px;
        display: grid; place-items: center;
        box-shadow: 0 8px 24px rgba(245,158,11,0.38);
        animation: badgeIn 0.6s 0.15s cubic-bezier(0.16,1,0.3,1) both;
    }

    @keyframes badgeIn {
        from { opacity: 0; transform: scale(0.7) rotate(-8deg); }
        to   { opacity: 1; transform: scale(1)   rotate(0deg);  }
    }

    .logo-badge svg { width: 26px; height: 26px; color: #0f172a; }

    /* ── HEADINGS ────────────────────────────────── */
    .register-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.03em;
        animation: fadeUp 0.5s 0.2s both;
    }
    .register-sub {
        font-size: 0.875rem;
        color: var(--muted);
        font-weight: 300;
        animation: fadeUp 0.5s 0.27s both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0);    }
    }

    /* ── LABEL ───────────────────────────────────── */
    .field-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 0.45rem;
        transition: color 0.2s;
    }

    /* ── INPUTS ──────────────────────────────────── */
    .field-wrap { position: relative; }

    .field-input {
        width: 100%;
        background: rgba(15, 23, 42, 0.6);
        border: 1.5px solid rgba(100, 116, 139, 0.28);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        color: var(--text);
        font-size: 0.9375rem;
        font-family: 'DM Sans', sans-serif;
        outline: none;
        transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
    }

    .field-input::placeholder { color: rgba(100,116,139,0.55); }

    .field-input:focus {
        border-color: var(--amber);
        background: rgba(15, 23, 42, 0.85);
        box-shadow: 0 0 0 3px rgba(245,158,11,0.14), 0 1px 4px rgba(0,0,0,0.3);
    }

    .field-input.has-icon { padding-right: 2.75rem; }

    /* Input valid state */
    .field-input.is-valid {
        border-color: rgba(52, 211, 153, 0.5);
    }
    .field-input.is-valid:focus {
        border-color: var(--success);
        box-shadow: 0 0 0 3px rgba(52,211,153,0.12), 0 1px 4px rgba(0,0,0,0.3);
    }

    /* focused label colour */
    .field-group:focus-within .field-label { color: var(--amber-lt); }

    /* ── PASSWORD TOGGLE ─────────────────────────── */
    .pw-toggle {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        display: grid;
        place-items: center;
        transition: color 0.2s;
    }
    .pw-toggle:hover { color: var(--amber-lt); }
    .pw-toggle svg { width: 18px; height: 18px; }

    /* ── PASSWORD STRENGTH ───────────────────────── */
    .strength-bar-wrap {
        display: flex;
        gap: 5px;
        margin-top: 8px;
    }

    .strength-segment {
        flex: 1;
        height: 3px;
        border-radius: 99px;
        background: rgba(100,116,139,0.2);
        transition: background 0.35s ease;
    }

    .strength-label {
        font-size: 0.72rem;
        margin-top: 5px;
        font-weight: 500;
        transition: color 0.3s;
    }

    /* ── CONFIRM MATCH INDICATOR ─────────────────── */
    .match-indicator {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        margin-top: 5px;
        font-weight: 500;
        min-height: 1.1rem;
        transition: color 0.3s;
    }
    .match-indicator svg { width: 12px; height: 12px; flex-shrink: 0; }

    /* ── ERROR TEXT ──────────────────────────────── */
    .field-error {
        font-size: 0.78rem;
        color: var(--error);
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        animation: shake 0.4s both;
    }

    @keyframes shake {
        0%,100% { transform: translateX(0); }
        20%      { transform: translateX(-5px); }
        40%      { transform: translateX(5px); }
        60%      { transform: translateX(-3px); }
        80%      { transform: translateX(3px); }
    }

    /* ── SUBMIT BUTTON ───────────────────────────── */
    .btn-register {
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 0.9375rem;
        letter-spacing: 0.04em;
        color: #0f172a;
        background: linear-gradient(135deg, var(--amber-lt) 0%, var(--amber-dk) 100%);
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform 0.18s, box-shadow 0.18s;
        box-shadow: 0 4px 20px rgba(245,158,11,0.35);
    }

    .btn-register::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.22) 50%, transparent 100%);
        transform: translateX(-100%);
        transition: transform 0.55s ease;
    }

    .btn-register:hover::before  { transform: translateX(100%); }
    .btn-register:hover          { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(245,158,11,0.45); }
    .btn-register:active         { transform: translateY(0px);  box-shadow: 0 4px 16px rgba(245,158,11,0.3); }

    .btn-register .spinner {
        display: none;
        width: 18px; height: 18px;
        border: 2.5px solid rgba(15,23,42,0.3);
        border-top-color: #0f172a;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        margin: 0 auto;
    }
    .btn-register.loading .btn-text { display: none; }
    .btn-register.loading .spinner  { display: block; }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── DIVIDER ─────────────────────────────────── */
    .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--border), transparent);
        margin: 0.25rem 0;
    }

    /* ── SESSION STATUS ──────────────────────────── */
    .session-status {
        background: rgba(245,158,11,0.09);
        border: 1px solid rgba(245,158,11,0.25);
        border-radius: 10px;
        padding: 0.6rem 0.9rem;
        font-size: 0.8375rem;
        color: var(--amber-lt);
        animation: fadeUp 0.4s both;
    }

    /* ── STAGGER ANIMATIONS ──────────────────────── */
    .anim-1 { animation: fadeUp 0.5s 0.32s both; }
    .anim-2 { animation: fadeUp 0.5s 0.39s both; }
    .anim-3 { animation: fadeUp 0.5s 0.46s both; }
    .anim-4 { animation: fadeUp 0.5s 0.53s both; }
    .anim-5 { animation: fadeUp 0.5s 0.58s both; }
    .anim-6 { animation: fadeUp 0.5s 0.63s both; }
    .anim-7 { animation: fadeUp 0.5s 0.68s both; }

    /* ── LINK ────────────────────────────────────── */
    .text-link {
        color: var(--amber-lt);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
        position: relative;
    }
    .text-link::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 1px;
        background: var(--amber-lt);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.25s ease;
    }
    .text-link:hover { color: #fff; }
    .text-link:hover::after { transform: scaleX(1); }
</style>

{{-- Override layout bg --}}
<style>
    /* Force full dark bg regardless of auth layout */
    body { background: #080c14 !important; }
    .bg-background { background: transparent !important; padding: 0 !important; }

    /* ── SPLIT LAYOUT ── */
    .auth-split {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    /* LEFT BRANDING PANEL — hidden on mobile */
    .auth-left {
        display: none;
    }

    /* RIGHT FORM PANEL */
    .auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.25rem;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    .auth-right-inner {
        width: 100%;
        max-width: 420px;
    }

    /* Desktop: show left panel */
    @media (min-width: 1024px) {
        .auth-left {
            display: flex;
            flex-direction: column;
            width: 46%;
            height: 100vh;
            min-height: 100vh;
            padding: 3rem;
            position: sticky;
            top: 0;
            overflow: hidden;
            background: linear-gradient(145deg, #0c1828 0%, #080c14 60%);
            border-right: 1px solid rgba(245,158,11,0.1);
        }

        .auth-right {
            width: 54%;
            flex: none;
            padding: 3rem 4rem;
        }

        .auth-right-inner {
            max-width: 480px;
        }
    }

    @media (min-width: 1280px) {
        .auth-right {
            padding: 3rem 5rem;
        }
    }
</style>

<div class="auth-split">

    {{-- ── LEFT BRANDING PANEL (desktop only) ── --}}
    <div class="auth-left">

        {{-- Background canvas for left panel --}}
        <canvas id="bg-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

        {{-- Logo (top) --}}
        <div style="position:relative;z-index:2;display:flex;align-items:center;">
            <a href="{{ url('/') }}" style="display:inline-block;">
                <img src="{{ asset('logos/logo.png') }}" alt="VESIT" style="height:38px; width:auto; object-fit:contain;" />
            </a>
        </div>

        {{-- Hero text --}}
        <div style="position:relative;z-index:2;flex:1;display:flex;flex-direction:column;justify-content:center;padding: 3rem 0;">
            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.3rem 0.85rem;border-radius:99px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);color:#fbbf24;font-size:0.72rem;font-weight:600;letter-spacing:0.04em;margin-bottom:1.5rem;width:fit-content;">
                <span style="width:6px;height:6px;border-radius:50%;background:#fbbf24;animation:pulse 2s infinite;"></span>
                Your campus, smarter
            </div>
            <h2 style="font-family:'Syne',sans-serif;font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;color:#fff;line-height:1.15;letter-spacing:-0.03em;margin-bottom:1.25rem;">
                Everything you need,<br><span style="color:#f59e0b;">all in one place.</span>
            </h2>
            <p style="color:#64748b;font-size:0.95rem;line-height:1.7;max-width:340px;">
                Notices, events, timetables, campus map and more — designed for students and faculty alike.
            </p>

            {{-- Feature pills --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.6rem;margin-top:2.5rem;">
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#94a3b8;font-size:0.78rem;"><i class="fa-solid fa-bell" style="color:#fbbf24;font-size:0.7rem;"></i> Notices</span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#94a3b8;font-size:0.78rem;"><i class="fa-solid fa-calendar-days" style="color:#60a5fa;font-size:0.7rem;"></i> Events</span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#94a3b8;font-size:0.78rem;"><i class="fa-solid fa-clock" style="color:#a78bfa;font-size:0.7rem;"></i> Timetable</span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#94a3b8;font-size:0.78rem;"><i class="fa-solid fa-map-location-dot" style="color:#34d399;font-size:0.7rem;"></i> Campus Map</span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#94a3b8;font-size:0.78rem;"><i class="fa-solid fa-comment-dots" style="color:#f87171;font-size:0.7rem;"></i> Complaints</span>
            </div>
        </div>

        {{-- Footer quote --}}
        <div style="position:relative;z-index:2;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.05);">
            <p style="color:#334155;font-size:0.78rem;">Trusted by students across VESIT.</p>
        </div>

    </div>

    {{-- ── RIGHT FORM PANEL ── --}}
    <div class="auth-right">

        {{-- Mobile-only background canvas --}}
        <canvas id="bg-canvas-mobile" style="position:absolute;inset:0;z-index:0;pointer-events:none;display:block;" class="lg:hidden"></canvas>

        <div class="auth-right-inner" style="position:relative;z-index:2;">

            <div
                class="register-card"
                x-data="{
                    showPassword: false,
                    showConfirm: false,
                    isLoading: false,
                    password: '',
                    confirm: '',

                    get strength() {
                        const p = this.password;
                        if (!p) return 0;
                        let score = 0;
                        if (p.length >= 8)                     score++;
                        if (/[A-Z]/.test(p))                   score++;
                        if (/[0-9]/.test(p))                   score++;
                        if (/[^A-Za-z0-9]/.test(p))            score++;
                        return score;
                    },

                    get strengthLabel() {
                        return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.strength] ?? '';
                    },

                    get strengthColor() {
                        return ['', '#f87171', '#fb923c', '#fbbf24', '#34d399'][this.strength] ?? '';
                    },

                    get passwordsMatch() {
                        return this.confirm.length > 0 && this.password === this.confirm;
                    },

                    get passwordsMismatch() {
                        return this.confirm.length > 0 && this.password !== this.confirm;
                    },

                    handleSubmit() {
                        this.isLoading = true;
                    }
                }"
            >

                <div class="flex justify-end -mt-3 mb-2">
                    <x-theme-toggle />
                </div>

                {{-- Logo + Title --}}
                <div class="flex flex-col items-center gap-4 mb-8 text-center">
                    <div class="logo-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="register-title">Create account</h1>
                        <p class="register-sub mt-1">Fill in your details to get started</p>
                    </div>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="session-status mb-5">{{ session('status') }}</div>
                @endif

                <form wire:submit="register" @submit="handleSubmit()" class="flex flex-col gap-5">

                    {{-- Name --}}
                    <div class="field-group anim-1">
                        <label for="name" class="field-label">Full name</label>
                        <div class="field-wrap">
                            <input
                                wire:model="name"
                                id="name"
                                type="text"
                                name="name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="John Doe"
                                class="field-input"
                            />
                        </div>
                        @error('name')
                            <p class="field-error">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="field-group anim-2">
                        <label for="email" class="field-label">Email address</label>
                        <div class="field-wrap">
                            <input
                                wire:model="email"
                                id="email"
                                type="email"
                                name="email"
                                required
                                autocomplete="email"
                                placeholder="you@example.com"
                                class="field-input"
                            />
                        </div>
                        @error('email')
                            <p class="field-error">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="field-group anim-3">
                        <label for="password" class="field-label">Password</label>
                        <div class="field-wrap">
                            <input
                                wire:model="password"
                                x-model="password"
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Create a password"
                                class="field-input has-icon"
                            />
                            <button type="button" class="pw-toggle" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" style="font-size:15px;"></i>
                            </button>
                        </div>

                        {{-- Strength bar --}}
                        <div x-show="password.length > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="strength-bar-wrap">
                                <template x-for="i in 4" :key="i">
                                    <div
                                        class="strength-segment"
                                        :style="i <= strength ? `background: ${strengthColor}; box-shadow: 0 0 6px ${strengthColor}55` : ''"
                                    ></div>
                                </template>
                            </div>
                            <p class="strength-label" :style="`color: ${strengthColor}`" x-text="strengthLabel"></p>
                        </div>

                        @error('password')
                            <p class="field-error">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="field-group anim-4">
                        <label for="password_confirmation" class="field-label">Confirm password</label>
                        <div class="field-wrap">
                            <input
                                wire:model="password_confirmation"
                                x-model="confirm"
                                id="password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Repeat your password"
                                :class="['field-input has-icon', passwordsMatch ? 'is-valid' : '']"
                            />
                            <button type="button" class="pw-toggle" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Hide password' : 'Show password'">
                                <i :class="showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" style="font-size:15px;"></i>
                            </button>
                        </div>

                        {{-- Match indicator --}}
                        <div x-show="confirm.length > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <p
                                class="match-indicator"
                                :style="passwordsMatch ? 'color: #34d399' : 'color: #f87171'"
                            >
                                {{-- Check --}}
                                <svg x-show="passwordsMatch" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                {{-- X --}}
                                <svg x-show="passwordsMismatch" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                <span x-text="passwordsMatch ? 'Passwords match' : 'Passwords do not match'"></span>
                            </p>
                        </div>

                        @error('password_confirmation')
                            <p class="field-error">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="divider anim-5"></div>

                    {{-- Submit --}}
                    <div class="anim-6">
                        <button
                            type="submit"
                            class="btn-register"
                            :class="{ 'loading': isLoading }"
                            wire:loading.class="loading"
                        >
                            <span class="btn-text">Create Account</span>
                            <span class="spinner"></span>
                        </button>
                    </div>

                </form>

                {{-- Login link --}}
                <p class="text-center text-sm text-slate-500 mt-6 anim-7">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-link ml-1">Sign in</a>
                </p>

            </div>
        </div>
    </div>

</div>

<script>
(() => {
    const AMBER = [245, 158, 11];
    const BLUE  = [56, 189, 248];

    function makeEngine(canvasEl) {
        if (!canvasEl) return;
        const ctx = canvasEl.getContext('2d');
        let W, H, particles = [], mouse = { x: -9999, y: -9999 };
        const COUNT = 80;

        // Reliably size canvas from parent bounding box
        const resize = () => {
            const parent = canvasEl.parentElement;
            const rect   = parent.getBoundingClientRect();
            W = canvasEl.width  = rect.width  || parent.offsetWidth  || window.innerWidth;
            H = canvasEl.height = rect.height || parent.offsetHeight || window.innerHeight;
        };

        class Particle {
            constructor() { this.reset(true); }
            reset(init = false) {
                this.x       = Math.random() * W;
                this.y       = init ? Math.random() * H : H + 10;
                this.vx      = (Math.random() - 0.5) * 0.3;
                this.vy      = -(0.28 + Math.random() * 0.5);
                this.r       = 0.8 + Math.random() * 2.2;
                this.life    = 0;
                this.maxLife = 200 + Math.random() * 200;
                this.color   = Math.random() > 0.55 ? AMBER : BLUE;
            }
            update() {
                this.x += this.vx; this.y += this.vy; this.life++;
                const dx = mouse.x - this.x, dy = mouse.y - this.y;
                const dist = Math.hypot(dx, dy);
                if (dist < 160) { this.vx += dx/dist*0.009; this.vy += dy/dist*0.009; }
                if (this.life > this.maxLife || this.y < -10) this.reset();
            }
            draw() {
                const alpha = Math.sin((this.life / this.maxLife) * Math.PI) * 0.6;
                const [r,g,b] = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${r},${g},${b},${alpha})`;
                ctx.fill();
            }
        }

        const drawOrbs = () => {
            [
                [W*.8,  H*.15, W*.35, [245,158,11], .06],
                [W*.15, H*.6,  W*.4,  [56,189,248],  .045],
                [W*.5,  H*.88, W*.28, [245,158,11],  .035],
            ].forEach(([x,y,r,c,a]) => {
                const g = ctx.createRadialGradient(x,y,0,x,y,r);
                g.addColorStop(0, `rgba(${c},${a})`);
                g.addColorStop(1, `rgba(${c},0)`);
                ctx.fillStyle = g;
                ctx.beginPath(); ctx.arc(x,y,r,0,Math.PI*2); ctx.fill();
            });
        };

        const drawGrid = () => {
            ctx.strokeStyle = 'rgba(245,158,11,0.025)';
            ctx.lineWidth = 1;
            for (let x = 0; x < W; x += 52) { ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x,H); ctx.stroke(); }
            for (let y = 0; y < H; y += 52) { ctx.beginPath(); ctx.moveTo(0,y); ctx.lineTo(W,y); ctx.stroke(); }
        };

        const loop = () => {
            ctx.clearRect(0,0,W,H);
            ctx.fillStyle = '#080c14'; ctx.fillRect(0,0,W,H);
            drawGrid();
            drawOrbs();
            particles.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(loop);
        };

        // Initial sizing after layout settles
        const start = () => {
            resize();
            particles = Array.from({ length: COUNT }, () => new Particle());
            loop();
        };

        window.addEventListener('resize', resize);
        window.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });

        // Use requestAnimationFrame to wait until the browser has laid out the panel
        requestAnimationFrame(() => requestAnimationFrame(start));
    }

    // Always init both — visibility controlled by CSS
    const leftCanvas   = document.getElementById('bg-canvas');
    const mobileCanvas = document.getElementById('bg-canvas-mobile');

    if (window.innerWidth >= 1024) {
        if (leftCanvas)   makeEngine(leftCanvas);
    } else {
        if (mobileCanvas) makeEngine(mobileCanvas);
    }
})();
</script>
