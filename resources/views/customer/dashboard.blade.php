@extends('layouts.app')

@section('title', 'My Account — Tokoku.id')

@section('styles')
    @vite(['resources/css/dashboard.css'])
    <style>
        .customer-dashboard-wrapper {
            padding-top: 120px;
            padding-bottom: var(--space-20);
            background: var(--bg-secondary);
            min-height: 80vh;
        }
    </style>
@endsection

@section('content')
<div class="customer-dashboard-wrapper">
    <div class="container">
        <!-- Dashboard Header -->
        <div class="section-header fade-in visible" style="text-align: left; margin-bottom: var(--space-8); max-width: 100%;">
            <div class="eyebrow" style="display: inline-flex; align-items: center; gap: var(--space-2); font-size: var(--text-sm); font-weight: var(--font-semibold); color: var(--primary-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: var(--space-4);">Customer Portal</div>
            <h1 class="page-title" style="font-size: var(--text-3xl); font-weight: var(--font-bold); color: var(--text-primary); margin-bottom: 2px;">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="section-subtitle" style="font-size: var(--text-base); color: var(--text-secondary); margin-top: 4px;">Manage your account details and track your recent orders in one place.</p>
        </div>

        <div class="dashboard-grid">
            <!-- Left Column: Profile Card -->
            <div class="card" style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-6); box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
                <h3 style="font-size: var(--text-lg); font-weight: var(--font-bold); border-bottom: 1px solid var(--border); padding-bottom: var(--space-3); margin-bottom: var(--space-4); display: flex; align-items: center; gap: var(--space-2); color: var(--text-primary);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--primary-500);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile Details
                </h3>
                <div style="display: flex; flex-direction: column; gap: var(--space-4); font-size: var(--text-sm);">
                    <div>
                        <span style="color: var(--text-secondary); display: block; font-size: var(--text-xs); font-weight: var(--font-semibold); text-transform: uppercase; letter-spacing: 0.05em;">Full Name</span>
                        <span style="color: var(--text-primary); font-weight: var(--font-medium); font-size: var(--text-base);">{{ auth()->user()->name }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-secondary); display: block; font-size: var(--text-xs); font-weight: var(--font-semibold); text-transform: uppercase; letter-spacing: 0.05em;">Email Address</span>
                        <span style="color: var(--text-primary); font-weight: var(--font-medium); font-size: var(--text-base);">{{ auth()->user()->email }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-secondary); display: block; font-size: var(--text-xs); font-weight: var(--font-semibold); text-transform: uppercase; letter-spacing: 0.05em;">Phone Number</span>
                        <span style="color: var(--text-primary); font-weight: var(--font-medium); font-size: var(--text-base);">{{ auth()->user()->phone ?? 'Not set' }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-secondary); display: block; font-size: var(--text-xs); font-weight: var(--font-semibold); text-transform: uppercase; letter-spacing: 0.05em;">Delivery Address</span>
                        <span style="color: var(--text-primary); font-weight: var(--font-medium); font-size: var(--text-base); line-height: 1.5;">{{ auth()->user()->address ?? 'No address saved yet.' }}</span>
                    </div>
                </div>
                <div style="margin-top: var(--space-8); display: flex; gap: var(--space-3);">
                    <a href="{{ route('profile') }}" class="btn btn-secondary" style="flex: 1; font-size: var(--text-xs); padding: var(--space-2) 0; height: 38px; justify-content: center;">Edit Profile</a>
                    <form method="POST" action="{{ route('logout') }}" style="flex: 1; display: flex;">
                        @csrf
                        <button type="submit" class="btn btn-danger-outline" style="width: 100%; font-size: var(--text-xs); padding: var(--space-2) 0; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-width: 1.5px; border-radius: var(--radius-lg);">Log Out</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Orders Card -->
            <div class="card" style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-6); box-shadow: var(--shadow-sm); min-height: 320px;">
                <h3 style="font-size: var(--text-lg); font-weight: var(--font-bold); border-bottom: 1px solid var(--border); padding-bottom: var(--space-3); margin-bottom: var(--space-4); display: flex; align-items: center; gap: var(--space-2); color: var(--text-primary);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--primary-500);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Recent Orders
                </h3>
                @if($recentOrders->isNotEmpty())
                    <div style="overflow-x: auto;">
                        <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr>
                                    <th style="padding: var(--space-3) var(--space-4); font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Order #</th>
                                    <th style="padding: var(--space-3) var(--space-4); font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Date</th>
                                    <th style="padding: var(--space-3) var(--space-4); font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Total</th>
                                    <th style="padding: var(--space-3) var(--space-4); font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Status</th>
                                    <th style="padding: var(--space-3) var(--space-4);"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr style="border-bottom: 1px solid var(--border);">
                                        <td style="padding: var(--space-4); font-weight: var(--font-bold); font-family: monospace; color: var(--text-primary);">{{ $order->order_number }}</td>
                                        <td style="padding: var(--space-4); color: var(--text-secondary);">{{ $order->created_at->format('d M Y') }}</td>
                                        <td style="padding: var(--space-4); font-weight: var(--font-semibold); color: var(--text-primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td style="padding: var(--space-4);">
                                            <span style="display: inline-block; padding: 2px 8px; border-radius: var(--radius-full); font-size: 10px; font-weight: var(--font-bold); text-transform: uppercase; background: {{ $order->order_status === 'delivered' ? 'rgba(16, 185, 129, 0.15)' : ($order->order_status === 'cancelled' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(99, 102, 241, 0.15)') }}; color: {{ $order->order_status === 'delivered' ? 'var(--success)' : ($order->order_status === 'cancelled' ? 'var(--danger)' : 'var(--primary-500)') }};">
                                                {{ $order->order_status }}
                                            </span>
                                        </td>
                                        <td style="padding: var(--space-4); text-align: right;">
                                            <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="btn btn-secondary" style="font-size: 11px; padding: 4px 10px; border-radius: var(--radius-md); height: auto; display: inline-flex;">Track</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: var(--space-12) 0; color: var(--text-secondary); display: flex; flex-direction: column; align-items: center; gap: var(--space-3);">
                        <div style="width: 64px; height: 64px; background: var(--primary-50); color: var(--primary-500); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <span style="font-weight: var(--font-medium);">You haven't placed any orders yet.</span>
                        <a href="{{ route('catalog') }}" class="btn btn-primary" style="font-size: var(--text-xs); padding: var(--space-2) var(--space-6); height: 38px; display: inline-flex; align-items: center; margin-top: var(--space-2);">Shop Now</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
