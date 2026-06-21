@section('title', 'Track Order — Tokoku.id')

@section('styles')
    <style>
        .track-page-wrapper {
            padding-top: 120px;
            padding-bottom: var(--space-20);
            background: var(--bg-secondary);
            min-height: 80vh;
        }
        .track-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .search-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-2xl);
            padding: var(--space-6);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--space-8);
            text-align: left;
        }
        .search-title {
            font-size: var(--text-lg);
            font-weight: var(--font-bold);
            color: var(--text-primary);
            margin-bottom: var(--space-4);
        }
        .search-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: var(--space-4);
            align-items: end;
        }
        @media (max-width: 640px) {
            .search-grid {
                grid-template-columns: 1fr;
            }
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: var(--space-2);
        }
        .form-group label {
            font-size: var(--text-xs);
            font-weight: var(--font-bold);
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-input {
            width: 100%;
            height: 44px;
            padding: 0 var(--space-4);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            font-size: var(--text-sm);
            color: var(--text-primary);
            background: white;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary-500);
        }
        .btn-track {
            height: 44px;
            font-weight: var(--font-bold);
            padding: 0 var(--space-8);
        }
        .error-card {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
            border-radius: var(--radius-lg);
            padding: var(--space-4);
            margin-bottom: var(--space-8);
            font-size: var(--text-sm);
            font-weight: var(--font-medium);
            text-align: left;
        }

        /* Timeline Box */
        .order-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            box-shadow: var(--shadow-sm);
            text-align: left;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: var(--space-4);
            margin-bottom: var(--space-6);
            flex-wrap: wrap;
            gap: var(--space-2);
        }
        .order-title {
            font-size: var(--text-lg);
            font-weight: var(--font-bold);
            color: var(--text-primary);
        }
        .order-number {
            font-family: 'JetBrains Mono', monospace;
            color: var(--primary-600);
        }
        .order-date {
            font-size: var(--text-xs);
            color: var(--text-secondary);
        }

        /* Timeline Visual */
        .timeline-wrapper {
            margin-bottom: var(--space-8);
            padding: var(--space-4) 0;
        }
        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: var(--space-4);
        }
        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 50px;
            right: 50px;
            height: 4px;
            background: var(--gray-200);
            z-index: 1;
        }
        .timeline-progress-bar {
            position: absolute;
            top: 18px;
            left: 50px;
            height: 4px;
            background: var(--success);
            z-index: 2;
            transition: width 0.4s ease;
        }
        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 3;
            width: 100px;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: var(--gray-100);
            border: 3px solid white;
            outline: 2px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-weight: var(--font-bold);
            font-size: var(--text-xs);
            transition: all 0.3s ease;
        }
        .timeline-step.active .step-circle {
            background: var(--success);
            color: white;
            outline-color: var(--success);
        }
        .timeline-step.current .step-circle {
            background: var(--primary-500);
            color: white;
            outline-color: var(--primary-500);
        }
        .step-label {
            font-size: 11px;
            font-weight: var(--font-bold);
            color: var(--text-secondary);
            margin-top: var(--space-2);
            text-align: center;
        }
        .timeline-step.active .step-label, .timeline-step.current .step-label {
            color: var(--text-primary);
        }

        /* Order Cancelled block */
        .cancelled-banner {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid var(--danger);
            color: var(--danger);
            border-radius: var(--radius-xl);
            padding: var(--space-4);
            margin-bottom: var(--space-6);
            display: flex;
            align-items: center;
            gap: var(--space-3);
            font-weight: var(--font-semibold);
            font-size: var(--text-sm);
        }

        /* Details list */
        .info-section {
            margin-bottom: var(--space-6);
        }
        .info-title {
            font-size: var(--text-sm);
            font-weight: var(--font-bold);
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-3);
            border-bottom: 1px solid var(--border);
            padding-bottom: var(--space-2);
        }
        .tracking-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-6);
        }
        @media (max-width: 640px) {
            .tracking-info-grid {
                grid-template-columns: 1fr;
            }
        }
        .info-card {
            background: var(--gray-50);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: var(--space-5);
        }
        .info-card p {
            font-size: var(--text-sm);
            line-height: 1.6;
            margin-bottom: var(--space-2);
            color: var(--text-secondary);
        }
        .info-card p strong {
            color: var(--text-primary);
        }

        /* Timeline History */
        .history-list {
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
            position: relative;
            padding-left: var(--space-6);
            border-left: 2px solid var(--border);
            margin-left: 10px;
            margin-top: var(--space-4);
        }
        .history-item {
            position: relative;
        }
        .history-item::before {
            content: '';
            position: absolute;
            left: -31px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: var(--radius-full);
            background: var(--border);
            border: 2px solid white;
            outline: 2px solid var(--border);
        }
        .history-item.first::before {
            background: var(--primary-500);
            outline-color: var(--primary-500);
        }
        .history-date {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: var(--font-medium);
        }
        .history-status {
            font-size: var(--text-xs);
            font-weight: var(--font-bold);
            color: var(--text-primary);
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .history-notes {
            font-size: var(--text-sm);
            color: var(--text-secondary);
        }
    </style>
@endsection

<div class="track-page-wrapper">
    <div class="container track-container">
        <!-- Search Form -->
        <div class="search-card">
            <h2 class="search-title">Track Your Order</h2>
            <form wire:submit.prevent="trackOrder" class="search-grid">
                <div class="form-group">
                    <label for="order_number">Order Number</label>
                    <input type="text" id="order_number" wire:model.defer="order_number" class="form-input" placeholder="ORD-YYYYMMDD-XXXXXX">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" wire:model.defer="email" class="form-input" placeholder="yourmail@example.com">
                </div>
                <button type="submit" class="btn btn-primary btn-track" wire:loading.attr="disabled">
                    <span wire:loading.remove>Track</span>
                    <span wire:loading>Loading...</span>
                </button>
            </form>
        </div>

        @if($errorMessage)
            <div class="error-card">
                {{ $errorMessage }}
            </div>
        @endif

        @if($order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h2 class="order-title">Order <span class="order-number">{{ $order->order_number }}</span></h2>
                        <span class="order-date">Placed on {{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="btn" style="padding: var(--space-2) var(--space-4); border-radius: var(--radius-full); font-size: var(--text-xs); text-transform: uppercase; pointer-events: none; background: {{ $order->order_status === 'delivered' ? 'var(--success)' : ($order->order_status === 'cancelled' ? 'var(--danger)' : 'var(--primary-600)') }}; color: white;">
                            {{ $order->order_status }}
                        </span>
                    </div>
                </div>

                @if($order->order_status === 'cancelled')
                    <div class="cancelled-banner">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        This order has been cancelled. If you need any assistance, please contact our support team.
                    </div>
                @else
                    <!-- Timeline Steps -->
                    @php
                        $statusIndex = [
                            'pending' => 0,
                            'processing' => 1,
                            'shipped' => 2,
                            'delivered' => 3
                        ][$order->order_status] ?? 0;
                        
                        $progressWidths = [0 => '0%', 1 => '33%', 2 => '66%', 3 => '100%'];
                        $progressWidth = $progressWidths[$statusIndex] ?? '0%';
                    @endphp

                    <div class="timeline-wrapper">
                        <div class="timeline-steps">
                            <div class="timeline-progress-bar" style="width: {{ $progressWidth }};"></div>
                            
                            <div class="timeline-step {{ $statusIndex >= 0 ? ($statusIndex == 0 ? 'current' : 'active') : '' }}">
                                <div class="step-circle">1</div>
                                <span class="step-label">Ordered</span>
                            </div>

                            <div class="timeline-step {{ $statusIndex >= 1 ? ($statusIndex == 1 ? 'current' : 'active') : '' }}">
                                <div class="step-circle">2</div>
                                <span class="step-label">Paid & Processing</span>
                            </div>

                            <div class="timeline-step {{ $statusIndex >= 2 ? ($statusIndex == 2 ? 'current' : 'active') : '' }}">
                                <div class="step-circle">3</div>
                                <span class="step-label">Shipped</span>
                            </div>

                            <div class="timeline-step {{ $statusIndex >= 3 ? ($statusIndex == 3 ? 'current' : 'active') : '' }}">
                                <div class="step-circle">4</div>
                                <span class="step-label">Delivered</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Shipping and Payment Details -->
                <div class="info-section">
                    <h3 class="info-title">Delivery & Payment</h3>
                    <div class="tracking-info-grid">
                        <div class="info-card">
                            <p style="margin-bottom: var(--space-3); font-weight: var(--font-bold); color: var(--text-primary);">Shipping Address</p>
                            @php
                                $address = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true);
                            @endphp
                            @if($address)
                                <p><strong>{{ $address['name'] }}</strong></p>
                                <p>{{ $address['phone'] }}</p>
                                <p>{{ $address['address'] }}, {{ $address['city'] }} - {{ $address['postal_code'] }}</p>
                            @endif

                            @if($order->tracking_number)
                                <div style="margin-top: var(--space-4); border-top: 1px solid var(--border); padding-top: var(--space-3);">
                                    <p><strong>Tracking Resi:</strong> <span style="font-family: 'JetBrains Mono', monospace; font-size: var(--text-sm); font-weight: var(--font-bold); color: var(--primary-600);">{{ $order->tracking_number }}</span></p>
                                </div>
                            @endif
                        </div>

                        <div class="info-card">
                            <p style="margin-bottom: var(--space-3); font-weight: var(--font-bold); color: var(--text-primary);">Order Summary</p>
                            <div style="display: flex; flex-direction: column; gap: var(--space-2);">
                                @foreach($order->items as $item)
                                    <div style="display: flex; justify-content: space-between; font-size: var(--text-xs);">
                                        <span>{{ $item->product_name }} x {{ $item->quantity }}</span>
                                        <span>Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                <div style="border-top: 1px solid var(--border); margin-top: var(--space-2); padding-top: var(--space-2); display: flex; justify-content: space-between; font-size: var(--text-sm); font-weight: var(--font-bold); color: var(--text-primary);">
                                    <span>Total:</span>
                                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status History Timeline -->
                @if($order->statusHistories->isNotEmpty())
                    <div class="info-section" style="margin-bottom: 0;">
                        <h3 class="info-title">Status Updates Log</h3>
                        <div class="history-list">
                            @foreach($order->statusHistories->sortByDesc('created_at') as $index => $history)
                                <div class="history-item {{ $index === 0 ? 'first' : '' }}">
                                    <div class="history-date">{{ $history->created_at->format('d M Y, H:i') }}</div>
                                    <div class="history-status">{{ $history->status }}</div>
                                    <div class="history-notes">{{ $history->notes }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
