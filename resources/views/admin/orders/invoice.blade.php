<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} — Tokoku.id</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/orders.css'])
</head>
<body class="invoice-body">

    <!-- Floating Print Button (Hidden during actual print) -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; padding: var(--space-4) var(--space-6); border-radius: var(--radius-lg); margin-bottom: var(--space-8); box-shadow: var(--shadow-sm);">
        <div>
            <span style="font-size: var(--text-sm); font-weight: 500; color: #475569;">Dokumen Invoice Resmi</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.close()" class="btn-outline" style="height: 38px; padding: 0 var(--space-4); border-radius: var(--radius-md);">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="btn-primary" style="height: 38px; padding: 0 var(--space-4); border-radius: var(--radius-md); background: var(--primary-600); border: none; color: white;">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Invoice Header -->
    <div class="invoice-header">
        <div>
            <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-2);">
                <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18" style="color: white;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span style="font-size: var(--text-xl); font-weight: 700; color: #0f172a; letter-spacing: -0.02em;">Tokoku.id</span>
            </div>
            <p style="font-size: var(--text-xs); color: #64748b; line-height: 1.5; max-width: 280px;">
                {{ \App\Models\Setting::where('key', 'contact_address')->first()?->value ?? 'Jl. Premium No. 1, Jakarta, Indonesia' }}<br>
                Telp: {{ \App\Models\Setting::where('key', 'contact_phone')->first()?->value ?? '+62 812-3456-7890' }}<br>
                Email: {{ \App\Models\Setting::where('key', 'contact_email')->first()?->value ?? 'support@tokoku.id' }}
            </p>
        </div>
        <div class="invoice-meta">
            <h1 class="invoice-title">Invoice</h1>
            <div style="margin-top: var(--space-3); display: flex; flex-direction: column; gap: 4px;">
                <div>
                    <span class="invoice-meta-label">No. Invoice:</span>
                    <span class="invoice-meta-value">{{ $order->order_number }}</span>
                </div>
                <div>
                    <span class="invoice-meta-label">Tanggal:</span>
                    <span class="invoice-meta-value">{{ $order->created_at->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="invoice-meta-label">Metode Bayar:</span>
                    <span class="invoice-meta-value" style="text-transform: uppercase;">{{ $order->payment_method ?: 'Midtrans Snap' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Section -->
    <div class="invoice-address-section">
        <div class="invoice-address-box">
            <h3>Tujuan Pengiriman</h3>
            <p>
                <strong>{{ $order->shipping_address['recipient_name'] ?? ($order->user ? $order->user->name : $order->guest_name) }}</strong><br>
                Telp: {{ $order->shipping_address['phone'] ?? ($order->user ? $order->user->phone : $order->guest_phone) }}<br>
                {{ $order->shipping_address['address_line'] ?? '' }}<br>
                {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }}<br>
                Kodepos: {{ $order->shipping_address['postal_code'] ?? '' }}
            </p>
        </div>
        <div class="invoice-address-box">
            <h3>Status Pembayaran</h3>
            <div style="margin-top: var(--space-2);">
                <span class="status-badge status-pay-{{ $order->payment_status }}" style="font-size: var(--text-sm); padding: 4px 12px;">
                    {{ strtoupper($order->payment_status) }}
                </span>
            </div>
            @if($order->tracking_number)
                <div style="margin-top: var(--space-4);">
                    <div class="invoice-meta-label">Nomor Resi (Kurir)</div>
                    <div class="invoice-meta-value" style="font-size: var(--text-sm); color: var(--primary-700);">{{ $order->tracking_number }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Purchased Items Table -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Varian</th>
                <th>SKU</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Harga Satuan</th>
                <th style="text-align: right;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td style="font-weight: 600; color: #0f172a;">{{ $item->product_name }}</td>
                    <td>{{ $item->variant_name ?: '-' }}</td>
                    <td style="font-family: monospace; font-size: var(--text-xs);">{{ $item->sku }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Area -->
    <table class="invoice-table-totals">
        <tr>
            <td style="color: #64748b;">Subtotal</td>
            <td style="text-align: right; font-weight: 500;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="color: #64748b;">Ongkos Kirim</td>
            <td style="text-align: right; font-weight: 500;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount_amount > 0)
            <tr>
                <td style="color: #64748b;">Diskon</td>
                <td style="text-align: right; color: #dc2626; font-weight: 500;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if($order->tax_amount > 0)
            <tr>
                <td style="color: #64748b;">Pajak</td>
                <td style="text-align: right; font-weight: 500;">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td>Total Bayar</td>
            <td style="text-align: right; color: #4f46e5;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="invoice-footer">
        <p>Terima kasih telah berbelanja di Tokoku.id!</p>
        <p style="margin-top: 4px; color: #94a3b8;">Jika Anda memiliki pertanyaan tentang invoice ini, silakan hubungi customer service kami.</p>
    </div>

    <!-- Auto-print Trigger -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto trigger print dialog on page load (excluding in dev testing frames if needed)
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
