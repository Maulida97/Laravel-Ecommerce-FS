<div class="mobile-overlay" id="mobileOverlay"></div>
<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
    <a href="{{ route('catalog') }}" class="nav-link {{ request()->routeIs('catalog') ? 'active' : '' }}">Shop</a>
    <a href="{{ route('home') }}#categories" class="nav-link">Categories</a>
    <a href="{{ route('home') }}#testimonials" class="nav-link">Reviews</a>
    <a href="{{ route('home') }}#newsletter" class="nav-link">Contact</a>
    @guest
        <a href="{{ route('login') }}" class="nav-link" style="margin-top:var(--space-4);">Login</a>
        <a href="{{ route('register') }}" class="btn btn-primary" style="margin-top:var(--space-2);width:100%;">Register</a>
    @else
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="margin-top:var(--space-4);width:100%;">Admin Panel</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top:var(--space-4);width:100%;">Dashboard</a>
        @endif
    @endguest
    
    <!-- Theme Toggle Mobile -->
    <div style="margin-top: var(--space-8); display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); padding-top: var(--space-4);">
        <span style="font-size: var(--text-sm); font-weight: var(--font-medium); color: var(--text-secondary);">Toggle Theme</span>
        <button id="theme-toggle-mobile" class="icon-btn" aria-label="Toggle theme" style="color: var(--text-secondary); display: inline-flex; align-items: center; justify-content: center; padding: 0; border: 1px solid var(--border); border-radius: var(--radius-md); width: 36px; height: 36px; background: var(--bg-secondary);">
            <!-- Sun icon (shows in dark mode) -->
            <svg id="theme-toggle-sun-mobile" class="hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
            <!-- Moon icon (shows in light mode) -->
            <svg id="theme-toggle-moon-mobile" class="hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>
</div>
