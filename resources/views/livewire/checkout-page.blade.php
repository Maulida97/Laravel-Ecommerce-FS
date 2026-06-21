@section('title', 'Checkout — Tokoku.id')

@section('styles')
    @vite(['resources/css/checkout.css'])
@endsection

<div class="checkout-page-wrapper">
    <div class="container">
        @if (session()->has('error'))
            <div class="alert-danger" style="background: var(--danger); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
                {{ session('error') }}
            </div>
        @endif

        <h1 class="page-title" style="margin-bottom: var(--space-8); font-size: var(--text-3xl); font-weight: var(--font-bold); text-align: left; color: var(--text-primary);">Checkout</h1>

        <div class="checkout-layout">
            <!-- Left Column: Shipping Form & Selection -->
            <div class="flex flex-col gap-6" style="display: flex; flex-direction: column; gap: var(--space-6);">
                <!-- Address Selection (if logged in and has addresses) -->
                @if(auth()->check() && $savedAddresses->isNotEmpty())
                    <div class="checkout-form-wrap" style="padding: var(--space-6);">
                        <div class="flex justify-between items-center mb-6" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6);">
                            <h2 class="font-bold text-lg text-text-primary flex items-center gap-2" style="margin: 0; padding: 0; border: none; font-size: var(--text-lg); font-weight: var(--font-bold);">
                                <span class="material-symbols-outlined text-primary-500">home_pin</span>
                                <span>Select Shipping Address</span>
                            </h2>
                            <a href="{{ route('dashboard.address-book') }}" class="text-primary-600 font-bold text-sm hover:underline" target="_blank">+ Add New</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-4);">
                            @foreach($savedAddresses as $addr)
                                @php
                                    $isSelected = ($address === $addr->address_line && $city === $addr->city && $postal_code === $addr->postal_code && $name === $addr->recipient_name);
                                @endphp
                                <div wire:click="selectAddress({{ $addr->id }})" 
                                     class="relative p-5 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50/50 dark:hover:bg-gray-800/10"
                                     style="border-radius: var(--radius-xl); padding: var(--space-5); border: 2px solid {{ $isSelected ? 'var(--primary-500)' : 'var(--border)' }}; background: {{ $isSelected ? 'rgba(99, 102, 241, 0.05)' : 'var(--bg-primary)' }};"
                                     wire:loading.class="opacity-60 pointer-events-none"
                                     wire:target="selectAddress">
                                    @if($isSelected)
                                        <div style="position: absolute; top: var(--space-4); right: var(--space-4);">
                                            <span class="material-symbols-outlined text-primary-600" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        </div>
                                    @endif
                                    <p class="font-bold text-text-muted uppercase tracking-wider font-label-caps" style="font-size: 10px; margin-bottom: var(--space-2);">{{ $addr->label }}</p>
                                    <p class="font-bold text-text-primary" style="font-size: var(--text-base);">{{ $addr->recipient_name }}</p>
                                    <p class="text-text-secondary mt-1 text-xs leading-relaxed" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $addr->address_line }}, {{ $addr->city }} {{ $addr->postal_code }}
                                    </p>
                                    <p class="text-text-secondary mt-2 text-xs font-semibold">{{ $addr->phone }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Shipping Form -->
                <div class="checkout-form-wrap">
                    <form wire:submit.prevent="placeOrder" class="checkout-form">
                        <h2 class="form-section-title">Shipping Details</h2>
                        
                        <div class="form-grid">
                            <!-- Name -->
                            <div class="form-group full-width">
                                <label for="name">Recipient Name</label>
                                <input type="text" id="name" wire:model.defer="name" class="form-input @error('name') input-error @enderror" placeholder="John Doe">
                                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" wire:model.defer="email" class="form-input @error('email') input-error @enderror" placeholder="johndoe@example.com">
                                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone -->
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" wire:model.defer="phone" class="form-input @error('phone') input-error @enderror" placeholder="08123456789">
                                @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <!-- Address -->
                            <div class="form-group full-width">
                                <label for="address">Full Address</label>
                                <textarea id="address" wire:model.defer="address" class="form-input textarea @error('address') input-error @enderror" placeholder="Street name, Building number, Apartment, Unit"></textarea>
                                @error('address') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <!-- City -->
                            <div class="form-group">
                                <label for="city">City / Region</label>
                                <input type="text" id="city" wire:model.defer="city" class="form-input @error('city') input-error @enderror" placeholder="Jakarta Selatan">
                                @error('city') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <!-- Postal Code -->
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" wire:model.defer="postal_code" class="form-input @error('postal_code') input-error @enderror" placeholder="12345">
                                @error('postal_code') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>

                            <!-- Notes -->
                            <div class="form-group full-width">
                                <label for="notes">Order Notes (Optional)</label>
                                <textarea id="notes" wire:model.defer="notes" class="form-input textarea-short @error('notes') input-error @enderror" placeholder="Notes about your delivery, e.g. special instructions for carrier."></textarea>
                                @error('notes') <span class="error-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-submit-order cursor-pointer" wire:loading.attr="disabled" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--space-2);">
                            <span wire:loading.remove>Place Order & Pay</span>
                            <span wire:loading style="display: none; align-items: center; justify-content: center; gap: var(--space-2);">
                                <svg class="animate-spin" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" style="opacity: 0.75;"></path>
                                </svg>
                                Processing Order...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Order Summary Sidebar -->
            <div class="checkout-summary-wrap" wire:loading.class="opacity-60 pointer-events-none" wire:target="selectAddress, placeOrder">
                <div class="summary-card">
                    <h2 class="summary-title">Order Items</h2>
                    
                    <div class="checkout-items-list">
                        @foreach($cart->items as $item)
                            <div class="checkout-item-row">
                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="checkout-item-img" loading="lazy">
                                <div class="checkout-item-details">
                                    <span class="checkout-item-name" title="{{ $item->product->name }}">{{ $item->product->name }}</span>
                                    @if($item->variant)
                                        <span class="checkout-item-variant">Variant: {{ $item->variant->variant_name }}</span>
                                    @endif
                                    <span class="checkout-item-qty">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                </div>
                                <span class="checkout-item-total">Rp {{ number_format($item->getTotal(), 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="summary-totals" style="margin-top: var(--space-6); border-top: 1px solid var(--border); padding-top: var(--space-4);">
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
                                    <span style="color: var(--success); font-weight: var(--font-semibold);">Free Shipping</span>
                                @endif
                            </span>
                        </div>
                        <div class="summary-total-row">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <!-- Midtrans Snap JS SDK -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('initiate-payment', event => {
                const payload = event.detail[0] || event.detail;
                const snapToken = payload.snapToken;
                const orderNumber = payload.orderNumber;

                if (!snapToken) return;

                if (snapToken.includes('dummy-snap-token')) {
                    console.log('Dummy Midtrans Token: Simulating checkout flow.');
                    window.location.href = `/checkout/success?order_id=${orderNumber}`;
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = `/checkout/success?order_id=${orderNumber}`;
                    },
                    onPending: function(result) {
                        window.location.href = `/order-track?order_number=${orderNumber}`;
                    },
                    onError: function(result) {
                        alert("Payment failed! Please try again.");
                    },
                    onClose: function() {
                        alert("You closed the payment popup before completing payment.");
                    }
                });
            });
        });
    </script>
@endsection
