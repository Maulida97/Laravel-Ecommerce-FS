@section('title', 'Order Success — Tokoku.id')

@section('styles')
    <style>
        .success-page-wrapper {
            padding-top: 120px;
            padding-bottom: var(--space-20);
            background: var(--bg-secondary);
            min-height: 80vh;
        }
        .success-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-2xl);
            padding: var(--space-10);
            box-shadow: var(--shadow-sm);
            max-width: 650px;
            margin: 0 auto;
            text-align: center;
        }
        .success-icon {
            width: 72px;
            height: 72px;
            background: var(--success);
            color: white;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-6);
        }
        .success-title {
            font-size: var(--text-2xl);
            font-weight: var(--font-bold);
            color: var(--text-primary);
            margin-bottom: var(--space-2);
        }
        .success-subtitle {
            font-size: var(--text-sm);
            color: var(--text-secondary);
            margin-bottom: var(--space-8);
        }
        .order-details-box {
            background: var(--gray-50);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: var(--space-6);
            margin-bottom: var(--space-8);
            text-align: left;
        }
        .details-title {
            font-size: var(--text-sm);
            font-weight: var(--font-bold);
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-4);
            border-bottom: 1px solid var(--border);
            padding-bottom: var(--space-2);
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            font-size: var(--text-sm);
            margin-bottom: var(--space-3);
        }
        .details-row span:first-child {
            color: var(--text-secondary);
        }
        .details-row span:last-child {
            font-weight: var(--font-semibold);
            color: var(--text-primary);
        }
        .order-items-mini-list {
            margin-top: var(--space-4);
            border-top: 1px solid var(--border);
            padding-top: var(--space-4);
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
        }
        .order-item-mini-row {
            display: flex;
            justify-content: space-between;
            font-size: var(--text-xs);
        }
        .order-item-mini-row span:first-child {
            color: var(--text-primary);
            font-weight: var(--font-medium);
        }
        .order-item-mini-row span:last-child {
            color: var(--text-secondary);
        }
        .success-actions {
            display: flex;
            gap: var(--space-4);
            justify-content: center;
        }
        .success-actions .btn {
            flex: 1;
            max-width: 200px;
        }
    </style>
@endsection

<div class="success-page-wrapper">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            
            <h1 class="success-title">Order Placed Successfully!</h1>
            <p class="success-subtitle">Thank you for your purchase. Your payment status will be updated automatically.</p>

            <div class="order-details-box">
                <h2 class="details-title">Order Information</h2>
                <div class="details-row">
                    <span>Order Number:</span>
                    <span>{{ $order->order_number }}</span>
                </div>
                <div class="details-row">
                    <span>Order Date:</span>
                    <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="details-row">
                    <span>Payment Status:</span>
                    <span style="text-transform: uppercase; color: {{ $order->payment_status === 'paid' ? 'var(--success)' : ($order->payment_status === 'pending' ? 'var(--warning)' : 'var(--danger)') }}">{{ $order->payment_status }}</span>
                </div>
                <div class="details-row">
                    <span>Amount Paid:</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="order-items-mini-list">
                    @foreach($order->items as $item)
                        <div class="order-item-mini-row">
                            <span>{{ $item->product_name }} {{ $item->variant_name ? '(' . $item->variant_name . ')' : '' }} x {{ $item->quantity }}</span>
                            <span>Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="success-actions">
                <a href="{{ route('catalog') }}" class="btn btn-secondary">Continue Shopping</a>
                <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="btn btn-primary">Track Order</a>
            </div>
        </div>
    </div>
</div>
