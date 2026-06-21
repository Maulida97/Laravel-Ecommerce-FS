<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Tokoku.id') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Auth Styles -->
    @vite(['resources/css/auth.css'])
</head>
<body class="auth-body">
    <div class="auth-layout">
        {{-- Left Side — Branding --}}
        <div class="auth-brand">
            <div class="brand-top">
                <div class="brand-logo">
                    <div class="brand-logo-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="brand-logo-text">Tokoku.id</span>
                </div>
            </div>

            <div class="brand-quote">
                <p class="brand-quote-text">"Tokoku.id telah mengubah cara saya mengelola toko. Dashboard yang intuitif dan fitur yang lengkap benar-benar membantu bisnis saya berkembang."</p>
                <div class="brand-quote-author">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face"
                         alt="Sari Dewi"
                         class="brand-quote-avatar">
                    <div>
                        <div class="brand-quote-name">Sari Dewi</div>
                        <div class="brand-quote-role">Pemilik Toko, BatikNusantara</div>
                    </div>
                </div>
            </div>

            <div class="brand-bottom">
                <div class="brand-stat">
                    <div class="brand-stat-num">10K+</div>
                    <div class="brand-stat-label">Merchant Aktif</div>
                </div>
                <div class="brand-stat-divider"></div>
                <div class="brand-stat">
                    <div class="brand-stat-num">Rp 5M+</div>
                    <div class="brand-stat-label">Total Transaksi</div>
                </div>
                <div class="brand-stat-divider"></div>
                <div class="brand-stat">
                    <div class="brand-stat-num">99.9%</div>
                    <div class="brand-stat-label">Uptime</div>
                </div>
            </div>
        </div>

        {{-- Right Side — Form --}}
        <div class="auth-form-side" style="background:#ffffff !important;">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
