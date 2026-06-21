@section('title', 'Shopping Cart — Tokoku.id')

@section('styles')
    @vite(['resources/css/cart.css'])
@endsection

<div class="cart-page-wrapper">
    <div class="container">
        <!-- Toast Alerts -->
        @if (session()->has('success'))
            <div class="alert-success" style="background: var(--success); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
                {{ session('success') }}
            </div>
        @endif

        <h1 class="page-title" style="margin-bottom: var(--space-8); font-size: var(--text-3xl); font-weight: var(--font-bold); text-align: left;">Shopping Cart</h1>

        @if($cart && $cart->items->isNotEmpty())
            <div class="cart-layout">
                <!-- Left Column: Items Table -->
                <div class="cart-items-wrap">
                    <div class="cart-table-container" style="position: relative;">
                        <!-- Table Loading Overlay -->
                        <div wire:loading class="table-loading-overlay">
                            <div class="spinner-dual"></div>
                        </div>
                        <table class="cart-table" wire:loading.class="opacity-60 pointer-events-none">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart->items as $item)
                                    <tr>
                                        <!-- Product Info -->
                                        <td class="product-cell">
                                            <div class="product-meta">
                                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="product-image" loading="lazy">
                                                <div class="product-details">
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="product-name-link">{{ $item->product->name }}</a>
                                                    @if($item->variant)
                                                        <span class="product-variant-label">Variant: {{ $item->variant->variant_name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Price -->
                                        <td class="price-cell">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>

                                        <!-- Quantity Selector -->
                                        <td class="qty-cell">
                                            <div class="qty-control">
                                                <button type="button" class="qty-btn" wire:click="decrementItem({{ $item->id }})" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                </button>
                                                <input type="number" class="qty-input" value="{{ $item->quantity }}" readonly>
                                                @php
                                                    $maxStock = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
                                                @endphp
                                                <button type="button" class="qty-btn" wire:click="incrementItem({{ $item->id }})" {{ $item->quantity >= $maxStock ? 'disabled' : '' }}>
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                            </div>
                                            @if($item->quantity >= $maxStock)
                                                <span class="max-stock-warning">Max Stock</span>
                                            @endif
                                        </td>

                                        <!-- Total -->
                                        <td class="total-cell">
                                            Rp {{ number_format($item->getTotal(), 0, ',', '.') }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="action-cell">
                                            <button type="button" class="remove-btn" wire:click="removeItem({{ $item->id }})" title="Remove item">
                                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="cart-actions-row">
                        <a href="{{ route('catalog') }}" class="btn btn-secondary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: var(--space-2);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Continue Shopping
                        </a>
                        <button type="button" class="btn btn-danger-outline" wire:click="clearCart" wire:confirm="Are you sure you want to clear your cart?">
                            Clear Shopping Cart
                        </button>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="cart-summary-wrap">
                    <div class="summary-card" wire:loading.class="opacity-60 pointer-events-none">
                        <h2 class="summary-title">Order Summary</h2>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>
                                @if($shippingCost > 0)
                                    Rp {{ number_format($shippingCost, 0, ',', '.') }}
                                @else
                                    <span class="free-shipping-label">Free Shipping</span>
                                @endif
                            </span>
                        </div>

                        @if($shippingCost > 0)
                            <div class="shipping-info-alert">
                                Add <strong>Rp {{ number_format(500000 - $subtotal, 0, ',', '.') }}</strong> more to get Free Shipping!
                            </div>
                        @endif

                        <div class="summary-total-row">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout') }}" class="btn btn-primary btn-checkout">
                            Proceed to Checkout
                        </a>

                        <div class="trust-badges">
                            <div class="badge-item">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Secure Payment
                            </div>
                            <div class="badge-item">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Guarantee Protection
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart-state">
                <div class="empty-cart-icon">
                    <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary">Shop Now</a>
            </div>
        @endif
    </div>
</div>
