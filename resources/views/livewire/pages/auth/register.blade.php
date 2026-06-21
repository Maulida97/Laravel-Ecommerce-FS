<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Header --}}
    <div class="auth-header">
        <h1>Buat Akun</h1>
        <p>Sudah punya akun? <a href="{{ route('login') }}" wire:navigate>Masuk di sini</a></p>
    </div>

    {{-- Validation Errors Banner --}}
    @if ($errors->any())
        <div class="auth-status" style="background: var(--danger-bg); border-color: rgba(239,68,68,0.2); color: var(--danger); margin-bottom: 1.5rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form wire:submit="register">
        {{-- Full Name --}}
        <div class="auth-form-group">
            <label class="auth-form-label" for="name">Nama Lengkap</label>
            <div class="auth-input-wrap">
                <input
                    type="text"
                    class="auth-form-input with-icon @error('name') error @enderror"
                    id="name"
                    wire:model="name"
                    placeholder="Nama lengkap Anda"
                    autocomplete="name"
                    autofocus
                    required>
                <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            @error('name')
                <div class="auth-error-msg">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="auth-form-group">
            <label class="auth-form-label" for="email">Alamat Email</label>
            <div class="auth-input-wrap">
                <input
                    type="email"
                    class="auth-form-input with-icon @error('email') error @enderror"
                    id="email"
                    wire:model="email"
                    placeholder="nama@contoh.com"
                    autocomplete="username"
                    required>
                <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
            </div>
            @error('email')
                <div class="auth-error-msg">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="auth-form-group">
            <label class="auth-form-label" for="password">Password</label>
            <div class="auth-input-wrap">
                <input
                    type="password"
                    class="auth-form-input with-icon with-icon-right @error('password') error @enderror"
                    id="password"
                    wire:model="password"
                    placeholder="Buat password yang kuat"
                    autocomplete="new-password"
                    oninput="checkAuthStrength(this.value)"
                    required>
                <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <button type="button" class="auth-input-icon-right" onclick="togglePwdReg('password', this)" aria-label="Tampilkan/sembunyikan password">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            {{-- Strength Meter --}}
            <div class="auth-strength-meter" id="authStrengthMeter" style="display:none;">
                <div class="auth-strength-bars">
                    <div class="auth-strength-bar" id="sBar1"></div>
                    <div class="auth-strength-bar" id="sBar2"></div>
                    <div class="auth-strength-bar" id="sBar3"></div>
                    <div class="auth-strength-bar" id="sBar4"></div>
                </div>
                <div class="auth-strength-text" id="authStrengthText">Masukkan password</div>
            </div>
            @error('password')
                <div class="auth-error-msg">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="auth-form-group">
            <label class="auth-form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="auth-input-wrap">
                <input
                    type="password"
                    class="auth-form-input with-icon with-icon-right @error('password_confirmation') error @enderror"
                    id="password_confirmation"
                    wire:model="password_confirmation"
                    placeholder="Ulangi password Anda"
                    autocomplete="new-password"
                    required>
                <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <button type="button" class="auth-input-icon-right" onclick="togglePwdReg('password_confirmation', this)" aria-label="Tampilkan/sembunyikan password">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <div class="auth-error-msg">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-btn-submit" wire:loading.attr="disabled" wire:loading.class="auth-btn-loading">
            Buat Akun
        </button>
    </form>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <script>
        function togglePwdReg(id, btn) {
            const input = document.getElementById(id);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
                ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>'
                : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        }

        function checkAuthStrength(password) {
            const meter  = document.getElementById('authStrengthMeter');
            const bars   = [document.getElementById('sBar1'), document.getElementById('sBar2'), document.getElementById('sBar3'), document.getElementById('sBar4')];
            const text   = document.getElementById('authStrengthText');
            if (!password) { meter.style.display = 'none'; return; }
            meter.style.display = 'block';
            let score = 0;
            if (password.length >= 8)          score++;
            if (/[A-Z]/.test(password))        score++;
            if (/[0-9]/.test(password))        score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            const classes = ['', 'weak', 'fair', 'good', 'strong'];
            const labels  = ['Terlalu lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            const colors  = ['#94a3b8', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            bars.forEach((bar, i) => {
                bar.className = 'auth-strength-bar';
                if (i < score) bar.classList.add(classes[score]);
            });
            text.textContent = labels[score];
            text.style.color = colors[score];
        }
    </script>
</div>
