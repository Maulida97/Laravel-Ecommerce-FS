@extends('layouts.app')

@section('styles')
    @vite(['resources/css/app.css', 'resources/css/dashboard.css'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <style>
        /* Styles for materials icons */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        aside a.bg-primary .material-symbols-outlined,
        aside a.bg-primary-container .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24;
        }
        .customer-portal-sidebar {
            min-height: calc(100vh - 80px);
        }
        @media (min-width: 768px) {
            .customer-portal-sidebar {
                display: flex !important;
            }
        }
        .max-w-max-width {
            max-width: 1280px;
        }
        .font-label-caps {
            font-size: 11px;
            letter-spacing: 0.05em;
        }
    </style>
    @yield('portal-styles')
@endsection

@section('content')
<div class="w-full flex pt-[80px] px-0" style="max-width: 100% !important;">
    <!-- SideNavBar -->
    <aside class="hidden md:flex flex-col gap-2 p-6 bg-surface-secondary border-r border-border w-64 customer-portal-sidebar flex-shrink-0" style="background-color: var(--bg-secondary);">
        <div class="mb-6 px-2">
            <div class="w-12 h-12 rounded-full mb-3 bg-primary-100 flex items-center justify-center overflow-hidden">
                @if(auth()->user()->avatar)
                    <img class="w-full h-full object-cover" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                @else
                    <div class="w-full h-full bg-primary-600 text-white flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <h3 class="font-semibold text-lg text-primary-600 leading-tight">Welcome back,</h3>
            <h4 class="font-bold text-xl text-text-primary truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</h4>
            <p class="text-xs text-text-muted mt-1 uppercase font-semibold tracking-wider font-label-caps">Customer Portal</p>
        </div>
        <nav class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard.profile') || request()->routeIs('dashboard') ? 'bg-primary-600 text-white shadow-md' : 'text-text-secondary hover:bg-primary-50 hover:text-primary-600 dark:hover:bg-primary-900/20 dark:hover:text-primary-300' }} rounded-lg font-bold transition-all" href="{{ route('dashboard.profile') }}">
                <span class="material-symbols-outlined">person</span>
                <span class="font-label-caps text-label-caps uppercase">Profile</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard.wishlist') ? 'bg-primary-600 text-white shadow-md' : 'text-text-secondary hover:bg-primary-50 hover:text-primary-600 dark:hover:bg-primary-900/20 dark:hover:text-primary-300' }} rounded-lg font-bold transition-all" href="{{ route('dashboard.wishlist') }}">
                <span class="material-symbols-outlined">favorite</span>
                <span class="font-label-caps text-label-caps uppercase">Wishlist</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard.orders') ? 'bg-primary-600 text-white shadow-md' : 'text-text-secondary hover:bg-primary-50 hover:text-primary-600 dark:hover:bg-primary-900/20 dark:hover:text-primary-300' }} rounded-lg font-bold transition-all" href="{{ route('dashboard.orders') }}">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="font-label-caps text-label-caps uppercase">My Orders</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard.address-book') ? 'bg-primary-600 text-white shadow-md' : 'text-text-secondary hover:bg-primary-50 hover:text-primary-600 dark:hover:bg-primary-900/20 dark:hover:text-primary-300' }} rounded-lg font-bold transition-all" href="{{ route('dashboard.address-book') }}">
                <span class="material-symbols-outlined">home_pin</span>
                <span class="font-label-caps text-label-caps uppercase">Address Book</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('checkout') ? 'bg-primary-600 text-white shadow-md' : 'text-text-secondary hover:bg-primary-50 hover:text-primary-600 dark:hover:bg-primary-900/20 dark:hover:text-primary-300' }} rounded-lg font-bold transition-all" href="{{ route('checkout') }}">
                <span class="material-symbols-outlined">shopping_basket</span>
                <span class="font-label-caps text-label-caps uppercase">Checkout</span>
            </a>
        </nav>
        <div class="mt-auto pt-4 border-t border-border">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-text-secondary hover:bg-danger hover:text-white rounded-lg transition-all text-left font-bold cursor-pointer">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-label-caps text-label-caps uppercase">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-grow p-6 md:p-12 bg-bg-primary min-w-0">
        @yield('portal-content')
        {{ $slot ?? '' }}
    </main>
</div>
@endsection
