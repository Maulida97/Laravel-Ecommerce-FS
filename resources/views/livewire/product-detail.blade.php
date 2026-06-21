@section('title', $product->name . ' — Tokoku.id')
@section('meta_description', $product->short_description ?? strip_tags(Str::limit($product->description, 150)))
@section('meta_keywords', $product->name . ', ' . ($product->category ? $product->category->name : 'Premium') . ', Tokoku.id, online shop')

@section('styles')
    @vite(['resources/css/product-detail.css'])
@endsection

<div class="product-detail-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrap" style="margin-bottom: var(--space-6);">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="breadcrumb-sep">/</span>
                <a href="{{ route('catalog') }}">Catalog</a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Toast Alerts -->
        @if (session()->has('success'))
            <div style="background: var(--success); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div style="background: var(--danger); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
                {{ session('error') }}
            </div>
        @endif

        <div class="product-detail-layout">
            <!-- Left Column: Gallery -->
            <div class="gallery-wrap" x-data="{ zoom: false }">
                <div class="main-image-frame">
                    <img src="{{ $activeImage }}" alt="{{ $product->name }}" id="main-product-image">
                </div>
                
                @if($product->images->count() > 1)
                    <div class="thumbnails-row">
                        @foreach($product->images as $img)
                            <button 
                                type="button" 
                                class="thumbnail-btn {{ $activeImage === $img->image_url ? 'active' : '' }}"
                                wire:click="setImage('{{ $img->image_url }}')"
                            >
                                <img src="{{ $img->image_url }}" alt="Thumbnail" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Column: Product Info -->
            <div class="info-wrap">
                <span class="product-category-badge">{{ $product->category->name ?? 'Premium' }}</span>
                <h1 class="product-title">{{ $product->name }}</h1>

                <div class="info-meta-row">
                    <div class="detail-stars">
                        @php
                            $rating = $product->rating ?? 4.5;
                            $fullStars = floor($rating);
                            $halfStar = ($rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                        @endphp
                        @for ($i = 0; $i < $fullStars; $i++)
                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                        @if ($halfStar)
                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endif
                        @for ($i = 0; $i < $emptyStars; $i++)
                            <svg viewBox="0 0 20 20" class="star-empty" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span style="font-size: var(--text-sm); color: var(--text-muted);">({{ $product->rating_count }} reviews)</span>
                    <span class="meta-divider"></span>
                    <div class="detail-sku">SKU: <span>{{ $this->variant ? $this->variant->sku : $product->sku }}</span></div>
                </div>

                <div class="detail-price-row">
                    <span class="detail-price">Rp {{ number_format($this->price, 0, ',', '.') }}</span>
                    @if($product->compare_at_price && $product->compare_at_price > $this->price)
                        <span class="detail-old-price">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                        @php
                            $discountPercentage = round((($product->compare_at_price - $this->price) / $product->compare_at_price) * 100);
                        @endphp
                        <span class="detail-discount-tag">-{{ $discountPercentage }}%</span>
                    @endif
                </div>

                <div class="detail-short-desc">
                    {{ $product->short_description ?? strip_tags(Str::limit($product->description, 180)) }}
                </div>

                <!-- Variant Selection -->
                @if($productAttributes->isNotEmpty())
                    <div class="variants-section">
                        @foreach($productAttributes as $attr)
                            <div class="variant-group">
                                <span class="variant-label">{{ $attr->name }}:</span>
                                <div class="variant-options-list">
                                    @foreach($attr->values as $val)
                                        @php
                                            $isActive = isset($selectedAttributes[$attr->id]) && $selectedAttributes[$attr->id] == $val->id;
                                        @endphp
                                        @if($attr->name === 'Color' && $val->color_code)
                                            <button 
                                                type="button" 
                                                class="color-swatch-btn {{ $isActive ? 'active' : '' }}" 
                                                style="background-color: {{ $val->color_code }};"
                                                wire:click="selectAttribute({{ $attr->id }}, {{ $val->id }})"
                                                title="{{ $val->value }}"
                                            ></button>
                                        @else
                                            <button 
                                                type="button" 
                                                class="text-swatch-btn {{ $isActive ? 'active' : '' }}"
                                                wire:click="selectAttribute({{ $attr->id }}, {{ $val->id }})"
                                            >
                                                {{ $val->value }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Actions (Quantity & Add to Cart) -->
                <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                    <div class="action-box">
                        <div class="qty-box">
                            <button type="button" class="qty-btn" wire:click="decrement" {{ $quantity <= 1 ? 'disabled' : '' }}>
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" class="qty-input" value="{{ $quantity }}" readonly>
                            <button type="button" class="qty-btn" wire:click="increment" {{ $quantity >= $this->stock ? 'disabled' : '' }}>
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>

                        @php
                            $stockCount = $this->stock;
                            $hasVariants = $product->variants->isNotEmpty();
                            $isFullySelected = !$hasVariants || $this->variant;
                        @endphp

                        <button 
                            type="button" 
                            class="btn btn-primary btn-detail-add-cart"
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            {{ $stockCount <= 0 || !$isFullySelected ? 'disabled' : '' }}
                        >
                            <span wire:loading.remove wire:target="addToCart">
                                @if($stockCount <= 0)
                                    Out of Stock
                                @elseif(!$isFullySelected)
                                    Select Options
                                @else
                                    Add to Cart
                                @endif
                            </span>
                            <span wire:loading wire:target="addToCart">Adding to Cart...</span>
                        </button>

                        <livewire:wishlist-button :product-id="$product->id" layout="button" :wire:key="'wishlist-btn-detail-'.$product->id" />
                    </div>

                    <!-- Stock Warning Level -->
                    <span class="stock-warning-label {{ $stockCount <= 0 ? 'out-of-stock' : ($stockCount <= 5 ? 'low-stock' : 'in-stock') }}">
                        @if($stockCount <= 0)
                            • Out of stock
                        @elseif($stockCount <= 5)
                            • Low stock! Only {{ $stockCount }} left in stock.
                        @else
                            • In stock ({{ $stockCount }} items available)
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Details Tab Section -->
        <div class="details-tabs-wrap" style="margin-top: var(--space-12);">
            <h2 class="tab-title">Product Specifications</h2>
            <div class="tab-content">
                {!! $product->description !!}
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->isNotEmpty())
            <div class="related-products-section">
                <h2 class="related-products-title">Related Products</h2>
                <div class="related-products-grid">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
