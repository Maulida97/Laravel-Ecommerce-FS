<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    {{-- Show success state if email was sent --}}
    @if (session('status'))
        <div class="auth-success-state">
            <div class="auth-success-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="auth-success-title">Cek Email Anda</h2>
            <p class="auth-success-text">
                Kami telah mengirimkan link reset password ke email Anda. Silakan cek inbox Anda.
            </p>
            <a href="{{ route('login') }}" wire:navigate class="auth-btn-submit" style="max-width:240px;margin:0 auto;text-decoration:none;">
                Kembali ke Masuk
            </a>
        </div>
    @else
        {{-- Header --}}
        <div class="auth-header">
            <h1>Reset Password</h1>
            <p>Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.</p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="auth-status" style="background: var(--danger-bg); border-color: rgba(239,68,68,0.2); color: var(--danger); margin-bottom: 1.5rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form wire:submit="sendPasswordResetLink">
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
                        autofocus
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

            {{-- Submit --}}
            <button type="submit" class="auth-btn-submit" wire:loading.attr="disabled" wire:loading.class="auth-btn-loading">
                Kirim Link Reset
            </button>
        </form>

        <a href="{{ route('login') }}" wire:navigate class="auth-back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke halaman masuk
        </a>
    @endif

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</div>
