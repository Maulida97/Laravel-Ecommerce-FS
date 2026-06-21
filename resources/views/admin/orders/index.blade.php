@extends('layouts.admin')

@section('title', 'Orders — Admin Dashboard')

@section('menu-orders-active', 'active')

@section('breadcrumb')
    <span>Orders</span> / <span>List</span>
@endsection

@section('styles')
    @vite(['resources/css/orders.css'])
@endsection

@section('content')
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert-toast" id="successAlert" style="position: fixed; top: 20px; right: 20px; background: var(--success); color: white; padding: 12px 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 1000; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-toast" id="errorAlert" style="position: fixed; top: 20px; right: 20px; background: var(--danger); color: white; padding: 12px 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 1000; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="page-header">
        <h1 class="page-title">Manage Orders</h1>
    </div>

    <!-- Advanced Filter Bar -->
    <div class="orders-filter-bar">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="filter-form">
            {{-- Search Filter --}}
            <div class="filter-group search-group">
                <label for="filterSearch" class="filter-label">Search</label>
                <input type="text" name="search" id="filterSearch" class="filter-input" placeholder="No. Pesanan, Nama, Email..." value="{{ request('search') }}">
            </div>
            
            {{-- Order Status Filter --}}
            <div class="filter-group">
                <label for="filterOrderStatus" class="filter-label">Order Status</label>
                <select name="order_status" id="filterOrderStatus" class="filter-input">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('order_status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('order_status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('order_status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('order_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="returned" {{ request('order_status') === 'returned' ? 'selected' : '' }}>Returned</option>
                </select>
            </div>

            {{-- Payment Status Filter --}}
            <div class="filter-group">
                <label for="filterPaymentStatus" class="filter-label">Payment Status</label>
                <select name="payment_status" id="filterPaymentStatus" class="filter-input">
                    <option value="">All Payments</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="expired" {{ request('payment_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            {{-- Date Range Filters --}}
            <div class="filter-group">
                <label for="filterDateFrom" class="filter-label">From</label>
                <input type="date" name="date_from" id="filterDateFrom" class="filter-input" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label for="filterDateTo" class="filter-label">To</label>
                <input type="date" name="date_to" id="filterDateTo" class="filter-input" value="{{ request('date_to') }}">
            </div>

            <button type="submit" class="btn-primary" style="height: 40px; margin-top: 19px; padding: 0 var(--space-4);">Filter</button>

            @if(request()->anyFilled(['search', 'order_status', 'payment_status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="btn-filter-reset" style="margin-top: 19px; text-decoration: none;">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="padding-left: 24px;">Order Number</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th style="width: 100px; text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="padding-left: 24px;">
                            <a href="{{ route('admin.orders.show', $order->id) }}" style="font-weight: 600; color: var(--primary-600); text-decoration: none;">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            @if($order->user)
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 500; color: var(--text-primary);">{{ $order->user->name }}</span>
                                    <span style="font-size: var(--text-xs); color: var(--text-secondary);">{{ $order->user->email }}</span>
                                </div>
                            @else
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 500; color: var(--text-primary);">{{ $order->guest_name }} <span style="font-size: var(--text-xs); background: var(--gray-100); color: var(--text-secondary); padding: 1px 4px; border-radius: var(--radius-sm); margin-left: 2px;">Guest</span></span>
                                    <span style="font-size: var(--text-xs); color: var(--text-secondary);">{{ $order->guest_email }}</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="color: var(--text-secondary);">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--text-primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="status-badge status-pay-{{ $order->payment_status }}">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-order-{{ $order->order_status }}">
                                {{ $order->order_status }}
                            </span>
                        </td>
                        <td style="text-align: right; padding-right: 24px;">
                            <div class="actions" style="justify-content: flex-end;">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="action-btn edit" aria-label="View {{ $order->order_number }}" style="background: var(--primary-50); color: var(--primary-600);">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="action-btn" aria-label="Print Invoice {{ $order->order_number }}" style="background: var(--gray-50); color: var(--text-secondary);">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            <div class="empty-state" style="padding: 64px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48" style="color: var(--text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <h3 style="font-size: var(--text-lg); font-weight: 600;">No orders found</h3>
                                <p style="color: var(--text-muted); font-size: var(--text-sm);">Try adjusting search query or filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($orders->count() > 0)
            <div class="pagination" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-top: 1px solid var(--border-light); background: var(--bg-primary); flex-wrap: wrap; gap: 12px;">
                <div class="pagination-info" style="font-size: var(--text-sm); color: var(--text-secondary);">
                    Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </div>
                <div class="pagination-btns" style="display: flex; align-items: center; gap: 4px;">
                    @if($orders->onFirstPage())
                        <span class="page-btn prev-next disabled" style="opacity: 0.5; pointer-events: none; border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="page-btn prev-next" style="border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                    @endif

                    @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 1), min($orders->lastPage(), $orders->currentPage() + 1)) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <span class="page-btn active" style="background: var(--primary-600); border-color: var(--primary-600); color: white; height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); font-weight: 500; padding: 0 8px;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn" style="border: 1px solid var(--border); background: var(--bg-primary); color: var(--text-secondary); height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); padding: 0 8px; text-decoration: none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="page-btn prev-next" style="border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    @else
                        <span class="page-btn prev-next disabled" style="opacity: 0.5; pointer-events: none; border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => successAlert.remove(), 500);
            }, 4000);
        }

        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                errorAlert.style.opacity = '0';
                errorAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => errorAlert.remove(), 500);
            }, 4000);
        }
    })();
</script>
@endsection
