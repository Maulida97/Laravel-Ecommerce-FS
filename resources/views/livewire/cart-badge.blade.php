<div style="position: relative;" x-data="{ open: false }" @click.away="open = false" class="cart-dropdown-container">
    <button class="icon-btn" aria-label="Cart" @click="open = !open" style="position: relative; cursor: pointer;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        @if($count > 0)
            <span class="cart-badge">{{ $count }}</span>
        @endif
    </button>

    <!-- Dropdown menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95" 
         class="mini-cart-dropdown" 
         style="display: none; position: absolute; right: 0; top: 100%; width: 340px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-xl); z-index: 1000; padding: var(--space-4); margin-top: var(--space-2);">
        
        <div class="mini-cart-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: var(--space-2); border-bottom: 1px solid var(--border); margin-bottom: var(--space-3);">
            <span style="font-weight: var(--font-bold); color: var(--text-primary); font-size: var(--text-sm);">Shopping Cart ({{ $count }})</span>
            <a href="{{ route('cart') }}" style="font-size: var(--text-xs); color: var(--primary-600); font-weight: var(--font-semibold);">View All</a>
        </div>

        @if($cart && $cart->items->isNotEmpty())
            <div class="mini-cart-items" style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: var(--space-3); padding-right: var(--space-1);">
                @foreach($cart->items as $item)
                    <div class="mini-cart-item" style="display: flex; gap: var(--space-3); align-items: center;">
                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border); flex-shrink: 0;">
                        <div class="item-info" style="flex: 1; min-width: 0; text-align: left;">
                            <a href="{{ route('products.show', $item->product->slug) }}" style="font-size: var(--text-xs); font-weight: var(--font-semibold); color: var(--text-primary); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->product->name }}</a>
                            @if($item->variant)
                                <span style="font-size: 10px; color: var(--text-secondary); display: block; margin-top: 2px;">Variant: {{ $item->variant->variant_name }}</span>
                            @endif
                            <span style="font-size: var(--text-xs); color: var(--text-secondary); display: block; margin-top: 2px;">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        </div>
                        <button type="button" wire:click="removeItem({{ $item->id }})" style="color: var(--danger); opacity: 0.7; cursor: pointer; padding: var(--space-1);" title="Remove">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
            
            <div class="mini-cart-footer" style="margin-top: var(--space-4); border-top: 1px solid var(--border); padding-top: var(--space-3);">
                <div style="display: flex; justify-content: space-between; font-weight: var(--font-bold); font-size: var(--text-sm); margin-bottom: var(--space-3);">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($cart->getSubtotal(), 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; gap: var(--space-2);">
                    <a href="{{ route('cart') }}" class="btn btn-secondary" style="flex: 1; font-size: var(--text-xs); padding: var(--space-2) 0; text-align: center; justify-content: center; height: 36px; display: inline-flex; align-items: center;">View Cart</a>
                    <a href="{{ route('checkout') }}" class="btn btn-primary" style="flex: 1; font-size: var(--text-xs); padding: var(--space-2) 0; text-align: center; justify-content: center; height: 36px; display: inline-flex; align-items: center;">Checkout</a>
                </div>
            </div>
        @else
            <div style="padding: var(--space-6) 0; text-align: center; color: var(--text-secondary); font-size: var(--text-sm);">
                Your cart is empty
            </div>
        @endif
    </div>
</div>
