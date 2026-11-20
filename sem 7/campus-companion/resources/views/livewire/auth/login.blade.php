<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.fullscreen')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = auth()->user();

        $redirect = match ($user->role) {
            'admin' => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            default => '/student/dashboard',
        };

        return redirect()->to($redirect);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::lower($this->email) . '|' . request()->ip();
    }
};
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap"
    rel="stylesheet">

<style>
    :root {
        --lime: #d2e823;
        --lime-lt: #e4f04e;
        --lime-dk: #b8cf10;
        --ink: #0c1018;
        --ink-soft: #111820;
        --err: #f87171;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html,
    body {
        min-height: 100vh;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--ink);
        -webkit-font-smoothing: antialiased;
        overflow-x: hidden;
    }

    /* ── FULL-SCREEN BACKGROUND ─────────────────── */
    .page {
        min-height: 100vh;
        width: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow-x: hidden;
    }

    /* Grid — same as landing */
    .bg-grid {
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.032) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.032) 1px, transparent 1px);
        background-size: 56px 56px;
        pointer-events: none;
        z-index: 0;
    }

    /* Orbs — same energy as landing hero */
    .orb {
        position: fixed;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
    }

    .orb-1 {
        width: 680px;
        height: 680px;
        top: -180px;
        right: -130px;
        background: radial-gradient(circle, rgba(210, 232, 35, 0.12) 0%, transparent 68%);
        animation: orb 9s ease-in-out infinite;
    }

    .orb-2 {
        width: 560px;
        height: 560px;
        bottom: -180px;
        left: -80px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.07) 0%, transparent 68%);
        animation: orb 11s ease-in-out infinite 2s;
    }

    .orb-3 {
        width: 300px;
        height: 300px;
        top: 50%;
        left: 40%;
        background: radial-gradient(circle, rgba(210, 232, 35, 0.055) 0%, transparent 70%);
        animation: orb 7s ease-in-out infinite 1s;
    }

    @keyframes orb {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: .65;
            transform: scale(1.08);
        }
    }

    /* ── NAVBAR ─────────────────────────────────── */
    .nav {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.85rem clamp(1.5rem, 5vw, 4rem);
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
    }

    .brand-icon {
        width: 34px;
        height: 34px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.09);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s;
    }

    .brand:hover .brand-icon {
        background: rgba(255, 255, 255, 0.1);
    }

    .brand-name {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.9);
        letter-spacing: -0.02em;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-a {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.4);
        text-decoration: none;
        padding: 0.45rem 0.8rem;
        border-radius: 8px;
        font-weight: 500;
        transition: color .2s, background .2s;
    }

    .nav-a:hover {
        color: rgba(255, 255, 255, .8);
        background: rgba(255, 255, 255, .05);
    }

    .nav-pill {
        font-size: 0.83rem;
        font-weight: 600;
        color: rgba(255, 255, 255, .75);
        text-decoration: none;
        padding: 0.45rem 1rem;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 99px;
        background: rgba(255, 255, 255, .05);
        transition: background .2s, border-color .2s, color .2s;
    }

    .nav-pill:hover {
        background: rgba(255, 255, 255, .09);
        border-color: rgba(255, 255, 255, .18);
        color: #fff;
    }

    /* ── CONTENT STAGE ───────────────────────────── */
    .content {
        position: relative;
        z-index: 5;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 2.5rem clamp(1.5rem, 5vw, 4.5rem) 4rem;
        box-sizing: border-box;
    }

    /* Two column — generous gap, PC-native feel with ample margins */
    .layout {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.15fr 460px;
        gap: clamp(2.5rem, 6vw, 6.5rem);
        align-items: center;
        box-sizing: border-box;
    }

    /* ── LEFT — Big headline block ─────────────── */
    .hl {
        animation: riseIn .65s cubic-bezier(.22, 1, .36, 1) both;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.85rem;
        border-radius: 99px;
        background: rgba(210, 232, 35, .1);
        border: 1px solid rgba(210, 232, 35, .2);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--lime);
        margin-bottom: 1.85rem;
    }

    .eb-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--lime);
        animation: blink 2.2s ease-in-out infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .3;
        }
    }

    .hl-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        color: #fff;
        line-height: .92;
        letter-spacing: -.045em;
        font-size: clamp(3.4rem, 5.2vw, 6rem);
        margin-bottom: 1.75rem;
    }

    .hl-title .acc {
        color: var(--lime);
    }

    .hl-body {
        font-size: clamp(.88rem, 1.1vw, 1rem);
        color: rgba(255, 255, 255, .4);
        line-height: 1.72;
        font-weight: 300;
        max-width: 400px;
        margin-bottom: 2.5rem;
    }

    /* Feature chips */
    .chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: .42rem .85rem;
        background: rgba(255, 255, 255, .04);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 99px;
        font-size: .77rem;
        color: rgba(255, 255, 255, .45);
        font-weight: 400;
        transition: border-color .2s, color .2s, background .2s;
    }

    .chip:hover {
        border-color: rgba(210, 232, 35, .25);
        color: rgba(255, 255, 255, .75);
        background: rgba(210, 232, 35, .04);
    }

    .chip svg {
        width: 12px;
        height: 12px;
        color: var(--lime);
        flex-shrink: 0;
    }

    /* ── RIGHT — Login card ──────────────────────── */
    .card-wrap {
        animation: riseIn .65s .1s cubic-bezier(.22, 1, .36, 1) both;
    }

    .card {
        background: rgba(15, 22, 32, .78);
        border: 1px solid rgba(255, 255, 255, .09);
        border-radius: 22px;
        padding: clamp(1.85rem, 2.5vw, 2.6rem) clamp(1.75rem, 2.2vw, 2.4rem);
        backdrop-filter: blur(36px);
        -webkit-backdrop-filter: blur(36px);
        box-shadow:
            0 0 0 1px rgba(210, 232, 35, .035),
            0 28px 70px rgba(0, 0, 0, .55),
            inset 0 1px 0 rgba(255, 255, 255, .05);
    }

    /* Card top line accent */
    .card::before {
        content: '';
        display: block;
        height: 2px;
        width: 48px;
        background: var(--lime);
        border-radius: 2px;
        margin-bottom: 1.6rem;
        opacity: .8;
    }

    .card-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1.65rem;
        color: #fff;
        letter-spacing: -.035em;
        margin-bottom: .3rem;
        line-height: 1.15;
    }

    .card-sub {
        font-size: .845rem;
        color: rgba(255, 255, 255, .32);
        font-weight: 300;
        margin-bottom: 1.85rem;
    }

    /* Session msg */
    .sess {
        padding: .6rem .9rem;
        background: rgba(210, 232, 35, .07);
        border: 1px solid rgba(210, 232, 35, .18);
        border-radius: 9px;
        font-size: .81rem;
        color: var(--lime);
        margin-bottom: 1.25rem;
    }

    /* Field */
    .field {
        margin-bottom: 1rem;
    }

    .field-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .38rem;
    }

    .f-lbl {
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .28);
        transition: color .2s;
    }

    .field:focus-within .f-lbl {
        color: var(--lime);
    }

    .f-link {
        font-size: .7rem;
        color: rgba(255, 255, 255, .22);
        text-decoration: none;
        font-weight: 500;
        transition: color .2s;
    }

    .f-link:hover {
        color: var(--lime);
    }

    .input-wrap {
        position: relative;
    }

    .f-in {
        width: 100%;
        background: rgba(255, 255, 255, .045);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 10px;
        padding: .76rem 1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .9rem;
        color: #fff;
        outline: none;
        transition: border-color .22s, background .22s, box-shadow .22s;
        -webkit-appearance: none;
    }

    .f-in:hover {
        border-color: rgba(255, 255, 255, .17);
    }

    .f-in:focus {
        border-color: var(--lime-dk);
        background: rgba(255, 255, 255, .068);
        box-shadow: 0 0 0 3px rgba(210, 232, 35, .1);
    }

    .f-in::placeholder {
        color: rgba(255, 255, 255, .17);
    }

    .f-in.icon-pad {
        padding-right: 2.65rem;
    }

    .pw-btn {
        position: absolute;
        right: .82rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: rgba(255, 255, 255, .2);
        display: flex;
        align-items: center;
        transition: color .2s;
    }

    .pw-btn:hover {
        color: var(--lime);
    }

    .pw-btn svg {
        width: 15px;
        height: 15px;
    }

    /* Error */
    .f-err {
        display: flex;
        align-items: center;
        gap: .35rem;
        margin-top: .35rem;
        padding: .35rem .7rem;
        background: rgba(248, 113, 113, .08);
        border-radius: 8px;
        font-size: .75rem;
        color: var(--err);
        animation: shake .4s both;
    }

    .f-err svg {
        width: 11px;
        height: 11px;
        flex-shrink: 0;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(-4px);
        }

        40% {
            transform: translateX(4px);
        }

        60% {
            transform: translateX(-3px);
        }

        80% {
            transform: translateX(2px);
        }
    }

    /* Remember */
    .rem-row {
        display: flex;
        align-items: center;
        gap: .6rem;
        margin: 1.1rem 0 1.4rem;
    }

    .chk {
        width: 15px;
        height: 15px;
        appearance: none;
        -webkit-appearance: none;
        background: rgba(255, 255, 255, .05);
        border: 1.5px solid rgba(255, 255, 255, .14);
        border-radius: 5px;
        cursor: pointer;
        flex-shrink: 0;
        position: relative;
        transition: background .18s, border-color .18s;
    }

    .chk:checked {
        background: var(--lime);
        border-color: var(--lime);
    }

    .chk:checked::after {
        content: '';
        position: absolute;
        left: 3px;
        top: 0;
        width: 4px;
        height: 8px;
        border: 2px solid var(--ink);
        border-top: none;
        border-left: none;
        transform: rotate(45deg);
    }

    .rem-row label {
        font-size: .82rem;
        color: rgba(255, 255, 255, .35);
        cursor: pointer;
        user-select: none;
    }

    /* Submit button */
    .btn {
        width: 100%;
        padding: .84rem 1.2rem;
        background: var(--lime);
        color: var(--ink);
        border: none;
        border-radius: 11px;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .91rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        position: relative;
        overflow: hidden;
        transition: background .2s, transform .18s, box-shadow .2s;
        box-shadow: 0 6px 26px rgba(210, 232, 35, .2);
        letter-spacing: .01em;
    }

    .btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg, transparent 30%, rgba(255, 255, 255, .28) 50%, transparent 70%);
        transform: translateX(-120%);
        transition: transform .58s ease;
    }

    .btn:hover {
        background: var(--lime-lt);
        transform: translateY(-2px);
        box-shadow: 0 12px 36px rgba(210, 232, 35, .3);
    }

    .btn:hover::before {
        transform: translateX(120%);
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn svg {
        width: 14px;
        height: 14px;
    }

    .spinner {
        width: 16px;
        height: 16px;
        border: 2.5px solid rgba(12, 16, 24, .25);
        border-top-color: var(--ink);
        border-radius: 50%;
        animation: spin .65s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Divider */
    .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .07), transparent);
        margin: 1.4rem 0;
    }

    /* Footer */
    .cf {
        text-align: center;
        font-size: .82rem;
        color: rgba(255, 255, 255, .26);
    }

    .cf a {
        color: var(--lime);
        text-decoration: none;
        font-weight: 600;
        margin-left: .22rem;
        transition: color .2s;
    }

    .cf a:hover {
        color: var(--lime-lt);
    }

    /* ── Page footer ── */
    .pg-foot {
        position: relative;
        z-index: 5;
        text-align: center;
        padding: 1.25rem 2rem 2rem;
        font-size: .72rem;
        color: rgba(255, 255, 255, .13);
    }

    /* ── Animations ── */
    @keyframes riseIn {
        from {
            opacity: 0;
            transform: translateY(22px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── RESPONSIVE ─────────────────────────────── */
    @media (max-width: 920px) {
        .layout {
            grid-template-columns: 1fr;
            max-width: 500px;
            gap: 2.5rem;
        }

        .hl {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hl-body {
            max-width: 100%;
        }

        .chips {
            justify-content: center;
        }
    }

    @media (max-width: 500px) {
        .hl-title {
            font-size: 2.8rem;
        }

        .card {
            border-radius: 16px;
            padding: 1.6rem 1.25rem;
        }

        .nav {
            padding: 1.4rem 1.2rem;
        }

        .nav-links .nav-a {
            display: none;
        }
    }

    /* ── LIGHT MODE OVERRIDES ── */
    html.light, [data-theme="light"] {
        --ink: #f8fafc;
        --ink-soft: #ffffff;
    }
    html.light .bg-grid {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 0, 0, 0.04) 1px, transparent 1px);
    }
    html.light .brand-icon {
        background: #ffffff;
        border-color: #e2e8f0;
    }
    html.light .brand-name {
        color: #0f172a;
    }
    html.light .nav-a {
        color: #64748b;
    }
    html.light .nav-a:hover {
        color: #0f172a;
        background: #f1f5f9;
    }
    html.light .nav-pill {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #0f172a;
    }
    html.light .hl-title {
        color: #0f172a;
    }
    html.light .hl-body {
        color: #475569;
    }
    html.light .chip {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #334155;
    }
    html.light .card {
        background: rgba(255, 255, 255, 0.92) !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08) !important;
    }
    html.light .card-title {
        color: #0f172a !important;
    }
    html.light .card-sub {
        color: #64748b !important;
    }
    html.light .f-lbl {
        color: #475569;
    }
    html.light .f-link {
        color: #64748b;
    }
    html.light .f-link:hover {
        color: #65a30d;
    }
    html.light .f-in {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    html.light .f-in:hover {
        border-color: #94a3b8;
    }
    html.light .f-in:focus {
        border-color: #65a30d;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(101, 163, 13, 0.15);
    }
    html.light .f-in::placeholder {
        color: #94a3b8;
    }
    html.light .rem-row label {
        color: #475569;
    }
    html.light .pw-btn {
        color: #64748b;
    }
    html.light .pw-btn:hover {
        color: #65a30d;
    }
    html.light .cf {
        color: #64748b !important;
    }
    html.light .pg-foot {
        color: #94a3b8 !important;
    }
</style>

<div class="page">

    <div class="bg-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    {{-- ── NAVBAR ── --}}
    <header>
        <nav class="nav">
            <a href="{{ url('/') }}" class="brand" style="text-decoration:none;">
                <img src="{{ asset('logos/logo.png') }}" alt="VESIT" style="height:36px; width:auto; object-fit:contain;" />
            </a>

            <div class="nav-links">
                <a href="{{ url('/') }}#features" class="nav-a">Features</a>
                <a href="{{ url('/') }}#faq" class="nav-a">FAQ</a>
                <x-theme-toggle />
            </div>
        </nav>
    </header>

    {{-- ── MAIN ── --}}
    <main class="content">
        <div class="layout">

            {{-- LEFT — headline --}}
            <div class="hl">
                <div class="eyebrow">
                    <span class="eb-dot"></span>
                    VESIT, University of Mumbai
                </div>

                <h1 class="hl-title">
                    Your<br>campus.<br>
                    <span class="acc">Reimagined.</span>
                </h1>

                <p class="hl-body">
                    Notices, events, timetable, and complaints — unified in one intelligent platform. Stop chasing
                    noticeboards.
                </p>

                <div class="chips">
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1" />
                        </svg>
                        Smart Notices
                    </span>
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                        Live Timetable
                    </span>
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M16 3v4M8 3v4M3 10h18" />
                        </svg>
                        Campus Events
                    </span>
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a4 4 0 11-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4z" />
                        </svg>
                        Complaint Tracking
                    </span>
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        Role-based Access
                    </span>
                </div>
            </div>

            {{-- RIGHT — login card --}}
            <div class="card-wrap">
                <div class="card" x-data="{ showPw: false, loading: false }">

                    <h2 class="card-title">Welcome back.</h2>
                    <p class="card-sub">Sign in to access your campus dashboard.</p>

                    @if (session('status'))
                        <div class="sess">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}" novalidate @submit="loading = true; document.getElementById('email').value = document.getElementById('email').value.replace(/['&quot;`\s]/g, '').trim()">
                        @csrf

                        {{-- Email --}}
                        <div class="field">
                            <div class="field-row">
                                <span class="f-lbl">Email address</span>
                            </div>
                            <div class="input-wrap">
                                <input name="email" id="email" type="text" inputmode="email" required autofocus
                                    oninput="this.value = this.value.replace(/['&quot;`]/g, '').trim()"
                                    value="{{ old('email') }}" autocomplete="email" placeholder="you@vesit.edu.in"
                                    class="f-in" />
                            </div>
                            @error('email')
                                <div class="f-err">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="field">
                            <div class="field-row">
                                <span class="f-lbl">Password</span>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="f-link">Forgot password?</a>
                                @endif
                            </div>
                            <div class="input-wrap">
                                <input name="password" id="password" :type="showPw ? 'text' : 'password'" required
                                    autocomplete="current-password" placeholder="••••••••••" class="f-in icon-pad" />
                                <button type="button" class="pw-btn" @click="showPw = !showPw">
                                    <svg x-show="!showPw" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-show="showPw" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="f-err">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Remember --}}
                        <div class="rem-row">
                            <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }} class="chk" />
                            <label for="remember" style="cursor: pointer;">Remember me</label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn" :disabled="loading">
                            <span x-show="!loading">
                                Sign In
                            </span>
                            <div x-show="loading" class="spinner" x-cloak></div>
                        </button>

                        <div class="divider"></div>

                        <p class="cf text-xs text-white/40 text-center">
                            Accounts are provisioned and managed by the campus administration.
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <footer class="pg-foot">
        Built for VESIT, University of Mumbai &nbsp;&middot;&nbsp; {{ date('Y') }}
    </footer>

</div>
