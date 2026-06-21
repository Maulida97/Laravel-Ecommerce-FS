@extends('layouts.admin')

@section('title', $customer->name . ' — Customer Statistics')

@section('menu-customers-active', 'active')

@section('breadcrumb')
    <span>Customers</span> / <span>Detail</span> / <span>{{ $customer->name }}</span>
@endsection

@section('styles')
    @vite(['resources/css/customers.css'])
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Customer Details & Stats</h1>
        <a href="{{ route('admin.customers.index') }}" class="btn-cancel" style="text-decoration: none; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>

    <div class="customer-detail-grid">
        <!-- Profile Column -->
        <div class="profile-card">
            <div class="profile-avatar-wrapper">
                @if($customer->avatar)
                    <img src="{{ $customer->avatar }}" alt="{{ $customer->name }}" class="profile-avatar">
                @else
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            
            <h2 class="profile-name">{{ $customer->name }}</h2>
            <span class="profile-role-badge">Customer</span>
            
            <div class="profile-divider"></div>
            
            <div class="profile-info-list">
                <div class="profile-info-item">
                    <div class="profile-info-label">Email Address</div>
                    <div class="profile-info-val">{{ $customer->email }}</div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-label">Phone Number</div>
                    <div class="profile-info-val">{{ $customer->phone ?: '-' }}</div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-label">Joined Date</div>
                    <div class="profile-info-val">{{ $customer->created_at->format('d M Y, H:i') }}</div>
                </div>
                @if($customer->bio)
                    <div class="profile-info-item">
                        <div class="profile-info-label">Biography</div>
                        <div class="profile-info-val profile-bio">"{{ $customer->bio }}"</div>
                    </div>
                @endif
                <div class="profile-info-item">
                    <div class="profile-info-label">Billing/Shipping Address</div>
                    <div class="profile-info-val">{{ $customer->address ?: 'No address registered' }}</div>
                </div>
            </div>
        </div>

        <!-- Statistics & History Column -->
        <div>
            <!-- Stats Metrics Grid -->
            <div class="detail-stats-grid">
                <div class="detail-stat-card">
                    <div class="detail-stat-icon" style="background: #ecfdf5; color: var(--success);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="detail-stat-info">
                        <span class="detail-stat-val">Rp {{ number_format($totalSpent, 0, ',', '.') }}</span>
                        <span class="detail-stat-label">Total Spent (Paid)</span>
                    </div>
                </div>
                <div class="detail-stat-card">
                    <div class="detail-stat-icon" style="background: var(--primary-50); color: var(--primary-500);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div class="detail-stat-info">
                        <span class="detail-stat-val">{{ $totalOrders }}</span>
                        <span class="detail-stat-label">Total Orders Placed</span>
                    </div>
                </div>
                <div class="detail-stat-card">
                    <div class="detail-stat-icon" style="background: #eff6ff; color: var(--info);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="detail-stat-info">
                        <span class="detail-stat-val">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</span>
                        <span class="detail-stat-label">Avg. Order Value (AOV)</span>
                    </div>
                </div>
            </div>

            <!-- Purchased Items Log ("Belanja apa saja") -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h3 class="detail-card-title">Purchased Items Log (Belanja Apa Saja)</h3>
                    <span class="badge-stat">{{ $purchasedItems->total() }} items total</span>
                </div>
                <div class="detail-card-body no-padding">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Order Number</th>
                                <th>Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchasedItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->variant_name)
                                            <div style="font-size: var(--text-xs); color: var(--text-muted);">Variant: {{ $item->variant_name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-stat">{{ $item->sku }}</span>
                                    </td>
                                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td><strong>Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $item->order_id) }}" style="color: var(--primary-600); font-weight: var(--font-semibold); text-decoration: underline;">
                                            #{{ $item->order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <span style="font-size: var(--text-xs); color: var(--text-secondary);">
                                            {{ $item->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: var(--space-6);">
                                        No purchase records found for this customer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($purchasedItems->count() > 0)
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing {{ $purchasedItems->firstItem() }}-{{ $purchasedItems->lastItem() }} of {{ $purchasedItems->total() }} items
                            </div>
                            <div class="pagination-btns">
                                @if($purchasedItems->onFirstPage())
                                    <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                                @else
                                    <a href="{{ $purchasedItems->previousPageUrl('items_page') }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                                @endif

                                @foreach ($purchasedItems->getUrlRange(max(1, $purchasedItems->currentPage() - 1), min($purchasedItems->lastPage(), $purchasedItems->currentPage() + 1)) as $page => $url)
                                    @if ($page == $purchasedItems->currentPage())
                                        <span class="page-btn active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($purchasedItems->hasMorePages())
                                    <a href="{{ $purchasedItems->nextPageUrl('items_page') }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                                @else
                                    <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order History List -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <h3 class="detail-card-title">Order History</h3>
                    <span class="badge-stat">{{ $orders->total() }} orders</span>
                </div>
                <div class="detail-card-body no-padding">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Subtotal</th>
                                <th>Shipping</th>
                                <th>Total Amount</th>
                                <th>Payment</th>
                                <th>Order Status</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $ord)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $ord->id) }}" style="color: var(--primary-600); font-weight: var(--font-bold); text-decoration: underline;">
                                            #{{ $ord->order_number }}
                                        </a>
                                    </td>
                                    <td>Rp {{ number_format($ord->subtotal, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($ord->shipping_cost, 0, ',', '.') }}</td>
                                    <td><strong>Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <span class="status-badge status-{{ $ord->payment_status }}">
                                            {{ ucfirst($ord->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: var(--text-xs); font-weight: var(--font-semibold); color: var(--text-primary); text-transform: uppercase;">
                                            {{ $ord->order_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: var(--text-xs); color: var(--text-secondary);">
                                            {{ $ord->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: var(--space-6);">
                                        No orders placed yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($orders->count() > 0)
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                            </div>
                            <div class="pagination-btns">
                                @if($orders->onFirstPage())
                                    <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                                @else
                                    <a href="{{ $orders->previousPageUrl('orders_page') }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                                @endif

                                @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 1), min($orders->lastPage(), $orders->currentPage() + 1)) as $page => $url)
                                    @if ($page == $orders->currentPage())
                                        <span class="page-btn active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl('orders_page') }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                                @else
                                    <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
