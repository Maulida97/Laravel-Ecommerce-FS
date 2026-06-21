<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard — Tokoku.id')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/admin-layout.css'])
    @yield('styles')
</head>
<body>
    <div class="app">
        <!-- Sidebar -->
        @include('layouts.partials.admin-sidebar')

        <!-- Overlay for mobile -->
        <div class="overlay" id="overlay"></div>

        <!-- Main Content Wrapper -->
        <main class="main">
            <!-- Header -->
            @include('layouts.partials.admin-header')

            <!-- Content Area -->
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Layout Scripts -->
    <script>
        (function() {
            'use strict';

            // Sidebar and Overlay toggle
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const overlay = document.getElementById('overlay');

            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                }
            }

            if (menuToggle) menuToggle.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });

            // Profile Dropdown Toggle
            const profileTrigger = document.getElementById('profileTrigger');
            const dropdownMenu = document.getElementById('dropdownMenu');

            if (profileTrigger && dropdownMenu) {
                profileTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isExpanded = profileTrigger.getAttribute('aria-expanded') === 'true';
                    profileTrigger.setAttribute('aria-expanded', !isExpanded);
                    dropdownMenu.classList.toggle('active');
                });

                document.addEventListener('click', () => {
                    profileTrigger.setAttribute('aria-expanded', 'false');
                    dropdownMenu.classList.remove('active');
                });
            }
        })();
    </script>
    @yield('scripts')
</body>
</html>
