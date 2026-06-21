@props(['product'])

@php
    $discountPercentage = 0;
    if ($product->compare_at_price && $product->compare_at_price > $product->price) {
        $discountPercentage = round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100);
    }
    
    $rating = $product->rating ?? 4.5;
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
@endphp

<div class="product-card fade-in visible">
    <div class="product-image-wrap">
        <a href="{{ route('products.show', $product->slug) }}" class="product-image-link" style="display: block; width: 100%; height: 100%;">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
            @if ($discountPercentage > 0)
                <span class="product-badge badge-sale">-{{ $discountPercentage }}%</span>
            @elseif ($product->is_featured)
                <span class="product-badge badge-new">New</span>
            @endif
        </a>
        <div class="product-actions">
            @php
                $hasVariants = $product->variants()->where('is_active', true)->exists();
            @endphp
            @if (request()->routeIs('catalog') && !$hasVariants)
                <button class="product-action-btn" wire:click.prevent="addToCart({{ $product->id }})" aria-label="Add to cart">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </button>
            @else
                <a href="{{ route('products.show', $product->slug) }}" class="product-action-btn" aria-label="Add to cart">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </a>
            @endif
            <livewire:wishlist-button :product-id="$product->id" :wire:key="'wishlist-btn-'.$product->id" />
        </div>
    </div>
    <a href="{{ route('products.show', $product->slug) }}" class="product-info-link" style="text-decoration: none; color: inherit; display: block; flex: 1;">
        <div class="product-info">
            <div class="product-category">{{ $product->category->name ?? 'Premium' }}</div>
            <h3 class="product-name" title="{{ $product->name }}">{{ $product->name }}</h3>
            <div class="product-price-row">
                <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="product-old-price">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                @endif
            </div>
            <div class="product-rating">
                <div class="stars">
                    @for ($i = 0; $i < $fullStars; $i++)
                        <svg viewBox="0 0 20 20" class="star-filled" fill="currentColor" style="color: var(--warning);"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    @if ($halfStar)
                        <svg viewBox="0 0 20 20" class="star-half" fill="currentColor" style="color: var(--warning);"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endif
                    @for ($i = 0; $i < $emptyStars; $i++)
                        <svg viewBox="0 0 20 20" class="star-empty" fill="currentColor" style="color: var(--gray-300);"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <span class="rating-count">({{ $product->rating_count }})</span>
            </div>
        </div>
    </a>
</div>
