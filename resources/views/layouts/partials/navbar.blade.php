<nav class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="logo">
            @if(!empty($settings['store_logo']))
                <img src="{{ $settings['store_logo'] }}" alt="Logo" style="height: 36px; max-width: 150px; object-fit: contain;">
            @else
                <div class="logo-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            @endif
            <span class="logo-text">{{ $settings['store_name'] ?? 'Tokoku.id' }}</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('catalog') }}" class="nav-link {{ request()->routeIs('catalog') ? 'active' : '' }}">Shop</a>
            <a href="{{ route('home') }}#categories" class="nav-link">Categories</a>
            <a href="{{ route('home') }}#testimonials" class="nav-link">Reviews</a>
            <a href="{{ route('home') }}#newsletter" class="nav-link">Contact</a>
        </div>
        <div class="nav-actions">
            <livewire:navbar-search />
            <livewire:cart-badge />
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="icon-btn" aria-label="Toggle theme" style="color: var(--text-secondary); display: inline-flex; align-items: center; justify-content: center; padding: 0;">
                <!-- Sun icon (shows in dark mode) -->
                <svg id="theme-toggle-sun" class="hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <!-- Moon icon (shows in light mode) -->
                <svg id="theme-toggle-moon" class="hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
            @guest
                <a href="{{ route('login') }}" class="nav-link" style="margin-right: var(--space-2);">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="display:inline-flex;">Register</a>
            @else
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="display:inline-flex;">Admin Panel</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary" style="display:inline-flex;">My Account</a>
                @endif
            @endguest
            <button class="hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
    </div>
</nav>
