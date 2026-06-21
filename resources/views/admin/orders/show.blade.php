@extends('layouts.admin')

@section('title', "Order {$order->order_number} — Admin Dashboard")

@section('menu-orders-active', 'active')

@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}" style="color: var(--text-secondary); text-decoration: none;">Orders</a> / <span>Detail</span>
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

    <div class="page-header" style="margin-bottom: var(--space-6);">
        <div>
            <a href="{{ route('admin.orders.index') }}" style="font-size: var(--text-sm); color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-bottom: var(--space-2);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Pesanan
            </a>
            <h1 class="page-title" style="margin: 0;">Order {{ $order->order_number }}</h1>
            <p style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: 2px;">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn-outline" style="text-decoration: none; padding: 0 var(--space-4);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Invoice
            </a>
        </div>
    </div>

    <div class="order-details-grid">
        {{-- Left Column: Main Details --}}
        <div>
            {{-- Items Card --}}
            <div class="details-card">
                <h2 class="details-card-title">Daftar Produk</h2>
                <div class="items-list">
                    @foreach($order->items as $item)
                        <div class="item-row">
                            <img src="{{ $item->product && $item->product->primaryImage ? $item->product->primaryImage->image_url : 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=100&h=100&fit=crop' }}" alt="{{ $item->product_name }}" class="item-img">
                            <div class="item-details">
                                <span class="item-name">{{ $item->product_name }}</span>
                                <div class="item-meta">
                                    <span>SKU: {{ $item->sku }}</span>
                                    @if($item->variant_name)
                                        <span style="margin-left: var(--space-2); background: var(--gray-100); padding: 1px 6px; border-radius: var(--radius-sm);">Varian: {{ $item->variant_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="item-price-qty">
                                <div class="item-price-unit">Rp {{ number_format($item->unit_price, 0, ',', '.') }} x {{ $item->quantity }}</div>
                                <div class="item-price-total">Rp {{ number_format($item->total_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Totals --}}
                <table class="totals-table">
                    <tr>
                        <td style="color: var(--text-secondary);">Subtotal</td>
                        <td style="text-align: right; font-weight: 500;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary);">Biaya Pengiriman</td>
                        <td style="text-align: right; font-weight: 500;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                        <tr>
                            <td style="color: var(--text-secondary);">Diskon</td>
                            <td style="text-align: right; color: var(--danger); font-weight: 500;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($order->tax_amount > 0)
                        <tr>
                            <td style="color: var(--text-secondary);">Pajak</td>
                            <td style="text-align: right; font-weight: 500;">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Pembayaran</td>
                        <td style="text-align: right;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            {{-- Address & Customer details --}}
            <div class="details-card">
                <h2 class="details-card-title">Informasi Pengiriman</h2>
                <div class="info-grid">
                    <div class="info-item" style="grid-column: span 2; border-bottom: 1px solid var(--border-light); padding-bottom: var(--space-3); margin-bottom: var(--space-2);">
                        <div class="info-label">Penerima</div>
                        <div class="info-value" style="font-size: var(--text-base);">{{ $order->shipping_address['recipient_name'] ?? ($order->user ? $order->user->name : $order->guest_name) }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: 2px;">
                            <span>Telp: {{ $order->shipping_address['phone'] ?? ($order->user ? $order->user->phone : $order->guest_phone) }}</span>
                            @if($order->user_id)
                                <span style="margin-left: var(--space-2); color: var(--primary-600); font-weight: 500;">(Pelanggan Terdaftar)</span>
                            @else
                                <span style="margin-left: var(--space-2); color: var(--text-muted); font-weight: 500;">(Guest Checkout)</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <div class="info-label">Alamat Pengiriman</div>
                        <div class="info-value" style="line-height: 1.5; font-weight: 400;">
                            {{ $order->shipping_address['address_line'] ?? '' }}<br>
                            {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }}<br>
                            Kodepos {{ $order->shipping_address['postal_code'] ?? '' }}
                        </div>
                    </div>
                    @if($order->notes)
                        <div class="info-item" style="grid-column: span 2; border-top: 1px solid var(--border-light); padding-top: var(--space-3); margin-top: var(--space-2);">
                            <div class="info-label">Catatan Pesanan</div>
                            <div class="info-value" style="font-weight: 400; font-style: italic; color: var(--text-secondary);">"{{ $order->notes }}"</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="details-card">
                <h2 class="details-card-title">Rincian Pembayaran</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value">{{ $order->payment_method ?: 'Midtrans Snap' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="info-value">
                            <span class="status-badge status-pay-{{ $order->payment_status }}">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                    </div>
                    @if($order->midtrans_transaction_id)
                        <div class="info-item">
                            <div class="info-label">ID Transaksi Midtrans</div>
                            <div class="info-value" style="font-family: monospace; font-size: var(--text-xs);">{{ $order->midtrans_transaction_id }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tipe / Status Midtrans</div>
                            <div class="info-value" style="text-transform: uppercase; font-size: var(--text-xs);">
                                {{ str_replace('_', ' ', $order->midtrans_payment_type) }} ({{ $order->midtrans_transaction_status }})
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Actions & Timeline --}}
        <div>
            {{-- Update Status Card --}}
            <div class="details-card" style="border-color: var(--primary-200); background: #fafaff;">
                <h2 class="details-card-title" style="color: var(--primary-700);">Kelola Pesanan</h2>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="action-form-group">
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <label for="orderStatusInput" class="filter-label">Status Pesanan</label>
                            <select name="order_status" id="orderStatusInput" class="filter-input" style="width: 100%;">
                                <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="returned" {{ $order->order_status === 'returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                        </div>

                        {{-- Tracking Input (shows when shipped) --}}
                        <div id="trackingInputWrap" style="display: {{ $order->order_status === 'shipped' ? 'flex' : 'none' }}; flex-direction: column; gap: 4px;">
                            <label for="trackingNumberInput" class="filter-label">Nomor Resi Pengiriman <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="tracking_number" id="trackingNumberInput" class="filter-input" placeholder="Contoh: JN123456789ID" value="{{ $order->tracking_number }}">
                            @error('tracking_number')
                                <span style="font-size: var(--text-xs); color: var(--danger); margin-top: 2px;">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Custom Notes --}}
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <label for="statusNotesInput" class="filter-label">Catatan Perubahan Status</label>
                            <textarea name="notes" id="statusNotesInput" class="filter-input" style="width: 100%; height: 80px; padding: var(--space-2); resize: none;" placeholder="Masukkan catatan opsional..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; margin-top: var(--space-2);">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            {{-- Separated Tracking Number Form (Visible if Shipped/Delivered to update resi only) --}}
            @if(in_array($order->order_status, ['shipped', 'delivered']))
                <div class="details-card">
                    <h2 class="details-card-title">Perbarui Resi Saja</h2>
                    <form action="{{ route('admin.orders.tracking', $order->id) }}" method="POST">
                        @csrf
                        <div class="action-form-group">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <label for="updateTrackingResi" class="filter-label">Nomor Resi</label>
                                <input type="text" name="tracking_number" id="updateTrackingResi" class="filter-input" style="width: 100%;" value="{{ $order->tracking_number }}" required>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <label for="updateTrackingNotes" class="filter-label">Catatan</label>
                                <input type="text" name="notes" id="updateTrackingNotes" class="filter-input" style="width: 100%;" placeholder="Contoh: Koreksi nomor resi.">
                            </div>
                            <button type="submit" class="btn-outline" style="width: 100%;">Perbarui Resi</button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Timeline Card --}}
            <div class="details-card">
                <h2 class="details-card-title">Riwayat Status</h2>
                <div class="timeline">
                    @forelse($order->statusHistories as $index => $history)
                        <div class="timeline-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <span class="timeline-status status-badge status-order-{{ $history->status }}" style="padding: 1px 6px;">
                                        {{ $history->status }}
                                    </span>
                                    <span class="timeline-date">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="timeline-notes" style="margin-top: 4px;">{{ $history->notes }}</div>
                                <div style="font-size: 10px; color: var(--text-muted); margin-top: 2px;">
                                    Oleh: {{ $history->user ? $history->user->name : 'Sistem Otomatis' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: var(--text-sm); color: var(--text-secondary); text-align: center;">Tidak ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>
        </div>
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

        // Toggle tracking number input in status form
        const orderStatusInput = document.getElementById('orderStatusInput');
        const trackingInputWrap = document.getElementById('trackingInputWrap');
        const trackingNumberInput = document.getElementById('trackingNumberInput');

        if (orderStatusInput && trackingInputWrap) {
            orderStatusInput.addEventListener('change', function() {
                if (orderStatusInput.value === 'shipped') {
                    trackingInputWrap.style.display = 'flex';
                    if (trackingNumberInput) trackingNumberInput.required = true;
                } else {
                    trackingInputWrap.style.display = 'none';
                    if (trackingNumberInput) trackingNumberInput.required = false;
                }
            });
        }
    })();
</script>
@endsection
