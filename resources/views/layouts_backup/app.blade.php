<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tokoku.id — Premium E-Commerce')</title>
    
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
        @yield('content')
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
        })();
    </script>
    @yield('scripts')
</body>
</html>
