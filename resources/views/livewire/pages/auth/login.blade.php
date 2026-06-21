<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        try {
            app(\App\Services\CartService::class)->mergeGuestCart(auth()->id());
        } catch (\Exception $e) {
            // Ignore errors merging cart
        }

        if (auth()->user()->role === 'admin') {
            $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Header --}}
    <div class="auth-header">
        <h1>Selamat Datang</h1>
        <p>Belum punya akun? <a href="{{ route('register') }}" wire:navigate>Daftar sekarang</a></p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    {{-- Validation Errors Banner --}}
    @if ($errors->any())
        <div class="auth-status" style="background: var(--danger-bg); border-color: rgba(239,68,68,0.2); color: var(--danger); margin-bottom: 1.5rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form wire:submit="login">
        {{-- Email --}}
        <div class="auth-form-group">
            <label class="auth-form-label" for="email">Alamat Email</label>
            <div class="auth-input-wrap">
                <input
                    type="email"
                    class="auth-form-input with-icon @error('form.email') error @enderror"
                    id="email"
                    wire:model="form.email"
                    placeholder="nama@contoh.com"
                    autocomplete="username"
                    autofocus
                    required>
                <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
            </div>
            @error('form.email')
                <div class="auth-error-msg">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="auth-form-group">
            <div class="auth-form-label-row">
                <label class="auth-form-label" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="auth-form-forgot">
                        Lupa password?
                    </a>
                @endif
            </div>
            <div class="auth-input-wrap">
                <input
                    type="password"
                    class="auth-form-input with-icon with-icon-right @error('form.password') error @enderror"
                    id="password"
                    wire:model="form.password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required>
                <svg class="auth-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <button type="button" class="auth-input-icon-right" id="toggleLoginPwd" onclick="togglePwd('password', this)" aria-label="Tampilkan/sembunyikan password">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('form.password')
                <div class="auth-error-msg">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="auth-form-group">
            <label class="auth-checkbox-wrap">
                <input type="checkbox" wire:model="form.remember" id="remember">
                <span class="auth-checkbox-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <span class="auth-checkbox-label">Ingat saya selama 30 hari</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-btn-submit" wire:loading.attr="disabled" wire:loading.class="auth-btn-loading">
            Masuk
        </button>
    </form>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <script>
        function togglePwd(id, btn) {
            const input = document.getElementById(id);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
                ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>'
                : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        }
    </script>
</div>
