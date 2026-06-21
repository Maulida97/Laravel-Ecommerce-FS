<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tokoku.id — Premium E-Commerce')</title>
    <meta name="description" content="@yield('meta_description', 'Tokoku.id — Premium E-Commerce dengan produk berkualitas tinggi.')">
    <meta name="keywords" content="@yield('meta_keywords', 'tokoku, e-commerce, belanja online, fashion, aksesoris')">
    
    <!-- Dark Mode Detection -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app-layout.css'])
    @yield('styles')
</head>
<body>
    <!-- Header/Navbar -->
    @include('layouts.partials.navbar')

    <!-- Mobile Menu -->
    @include('layouts.partials.mobile-menu')

    <!-- Main Content -->
    <main>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <!-- Footer -->
    <footer class="footer">
        @include('layouts.partials.footer')
    </footer>

    <!-- Layout Scripts -->
    <script>
        (function() {
            'use strict';

            // Navbar scroll effect
            const navbar = document.getElementById('navbar');
            function handleScroll() {
                navbar.classList.toggle('scrolled', window.scrollY > 50);
            }
            window.addEventListener('scroll', handleScroll);
            handleScroll();

            // Mobile menu
            const hamburger = document.getElementById('hamburger');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileOverlay = document.getElementById('mobileOverlay');

            function toggleMobileMenu() {
                hamburger.classList.toggle('active');
                mobileMenu.classList.toggle('open');
                mobileOverlay.classList.toggle('active');
                document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
            }

            hamburger.addEventListener('click', toggleMobileMenu);
            mobileOverlay.addEventListener('click', toggleMobileMenu);
            mobileMenu.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', toggleMobileMenu);
            });

            // Fade in on scroll
            const fadeElements = document.querySelectorAll('.fade-in');
            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        fadeObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            fadeElements.forEach(el => fadeObserver.observe(el));

            // Stagger children
            const staggerElements = document.querySelectorAll('.stagger-children');
            const staggerObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        staggerObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            staggerElements.forEach(el => staggerObserver.observe(el));

            // Theme toggle logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleSun = document.getElementById('theme-toggle-sun');
            const themeToggleMoon = document.getElementById('theme-toggle-moon');

            const themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');
            const themeToggleSunMobile = document.getElementById('theme-toggle-sun-mobile');
            const themeToggleMoonMobile = document.getElementById('theme-toggle-moon-mobile');

            function updateThemeUI() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    if (themeToggleSun) themeToggleSun.classList.remove('hidden');
                    if (themeToggleMoon) themeToggleMoon.classList.add('hidden');
                    if (themeToggleSunMobile) themeToggleSunMobile.classList.remove('hidden');
                    if (themeToggleMoonMobile) themeToggleMoonMobile.classList.add('hidden');
                } else {
                    if (themeToggleSun) themeToggleSun.classList.add('hidden');
                    if (themeToggleMoon) themeToggleMoon.classList.remove('hidden');
                    if (themeToggleSunMobile) themeToggleSunMobile.classList.add('hidden');
                    if (themeToggleMoonMobile) themeToggleMoonMobile.classList.remove('hidden');
                }
            }

            updateThemeUI();

            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
                updateThemeUI();
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', toggleTheme);
            }
            if (themeToggleBtnMobile) {
                themeToggleBtnMobile.addEventListener('click', toggleTheme);
            }
        })();
    </script>
    @yield('scripts')
</body>
</html>
