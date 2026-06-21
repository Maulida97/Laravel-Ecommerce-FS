@section('title', 'Product Catalog — Tokoku.id')

@section('styles')
    @vite(['resources/css/catalog.css'])
@endsection

<div class="catalog-page-wrapper" style="padding-top: 90px; padding-bottom: var(--space-16);">
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrap">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current">Catalog</span>
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

        <div class="catalog-layout">
            <!-- Sidebar Filters -->
            <aside class="sidebar">
                <div class="filter-card">
                    <h2 class="filter-title">
                        <span>Filters</span>
                        @if($search || count($selectedCategories) > 0 || $minPrice != $dbMinPrice || $maxPrice != $dbMaxPrice || $sortBy != 'newest')
                            <button wire:click="resetFilters" style="font-size: var(--text-xs); color: var(--danger); font-weight: var(--font-semibold); background: none; border: none; cursor: pointer; padding: 0;">Clear All</button>
                        @endif
                    </h2>

                    <!-- Search section -->
                    <div class="filter-section">
                        <h3 class="filter-section-title">Search</h3>
                        <div class="filter-search">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Type keyword...">
                        </div>
                    </div>

                    <!-- Categories section -->
                    <div class="filter-section">
                        <h3 class="filter-section-title">Categories</h3>
                        <div class="filter-list">
                            @foreach($categories as $category)
                                <label class="filter-item">
                                    <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->slug }}">
                                    <span class="filter-checkbox">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="filter-label">{{ $category->name }}</span>
                                    <span class="filter-count">{{ $category->products_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range section -->
                    <div class="filter-section">
                        <h3 class="filter-section-title">Price Range</h3>
                        <div class="price-slider-wrap">
                            <input type="range" 
                                   wire:model.live.debounce.300ms="maxPrice" 
                                   min="{{ $dbMinPrice }}" 
                                   max="{{ $dbMaxPrice }}" 
                                   step="{{ $priceStep }}"
                                   style="width: 100%; accent-color: var(--primary-500); cursor: pointer;"
                             >
                        </div>
                        <div class="price-inputs">
                            <input type="number" class="price-input" wire:model.live.debounce.300ms="minPrice" placeholder="Min">
                            <span class="price-sep">-</span>
                            <input type="number" class="price-input" wire:model.live.debounce.300ms="maxPrice" placeholder="Max">
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Product Grid / Main Area -->
            <div class="main-area">
                <div class="main-header">
                    <p class="result-count">
                        Showing <strong>{{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }}</strong> of <strong>{{ $products->total() }}</strong> products
                    </p>
                    <div class="main-controls">
                        <!-- Sort select -->
                        <select class="sort-select" wire:model.live="sortBy">
                            <option value="newest">Newest Arrivals</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="popular">Popularity</option>
                        </select>
                        
                        <!-- Layout / View Toggle -->
                        <div class="view-toggle">
                            <button wire:click="toggleLayout('grid')" class="view-btn {{ $layout === 'grid' ? 'active' : '' }}" aria-label="Grid view">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                            <button wire:click="toggleLayout('list')" class="view-btn {{ $layout === 'list' ? 'active' : '' }}" aria-label="List view">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Display Grid / List -->
                <div class="catalog-products-content" style="position: relative; min-height: 400px;">
                    <!-- Loading Skeletons -->
                    <div wire:loading class="skeleton-overlay">
                        @if($layout === 'grid')
                            <div class="product-grid">
                                @for ($i = 0; $i < 6; $i++)
                                    <div class="product-card skeleton-card">
                                        <div class="skeleton-image skeleton"></div>
                                        <div class="skeleton-info" style="padding: var(--space-4);">
                                            <div class="skeleton-text skeleton skeleton-title" style="margin-bottom: var(--space-2); height: 16px; border-radius: var(--radius-sm); width: 40%;"></div>
                                            <div class="skeleton-text skeleton skeleton-price" style="margin-bottom: var(--space-2); height: 20px; border-radius: var(--radius-sm); width: 75%;"></div>
                                            <div class="skeleton-text skeleton skeleton-rating" style="height: 14px; border-radius: var(--radius-sm); width: 50%;"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        @else
                            <div class="products-list-catalog" style="display: flex; flex-direction: column; gap: var(--space-6);">
                                @for ($i = 0; $i < 4; $i++)
                                    <div class="product-list-card skeleton-card" style="display: flex; gap: var(--space-6); padding: var(--space-6); align-items: center; background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-xl);">
                                        <div class="skeleton-image skeleton" style="width: 220px; min-width: 220px; aspect-ratio: 1; border-radius: var(--radius-lg);"></div>
                                        <div style="flex: 1; display: flex; flex-direction: column; gap: var(--space-3); padding: var(--space-4);">
                                            <div class="skeleton-text skeleton" style="height: 12px; width: 20%; border-radius: var(--radius-sm);"></div>
                                            <div class="skeleton-text skeleton" style="height: 24px; width: 60%; border-radius: var(--radius-sm);"></div>
                                            <div class="skeleton-text skeleton" style="height: 16px; width: 30%; border-radius: var(--radius-sm);"></div>
                                            <div class="skeleton-text skeleton" style="height: 14px; width: 40%; border-radius: var(--radius-sm);"></div>
                                        </div>
                                        <div style="width: 200px; padding: var(--space-6); border-left: 1px solid var(--border); display: flex; flex-direction: column; justify-content: center; gap: var(--space-4);">
                                            <div class="skeleton-text skeleton" style="height: 24px; width: 80%; margin: 0 auto; border-radius: var(--radius-sm);"></div>
                                            <div class="skeleton-text skeleton" style="height: 38px; width: 100%; border-radius: var(--radius-sm);"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        @endif
                    </div>

                    <div wire:loading.remove>
                        @if($products->isEmpty())
                            <div style="text-align: center; padding: 64px 24px; background: white; border: 1px solid var(--border); border-radius: var(--radius-xl);">
                                <svg style="width: 64px; height: 64px; color: var(--gray-300); margin-bottom: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">No products found</h3>
                                <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Try adjusting your filters or search keywords.</p>
                                <button wire:click="resetFilters" class="btn btn-primary" style="padding: 8px 16px; font-size: var(--text-sm);">Reset All Filters</button>
                            </div>
                        @else
                            @if($layout === 'grid')
                                <div class="product-grid">
                                    @foreach($products as $product)
                                        <x-product-card :product="$product" wire:key="grid-p-{{ $product->id }}" />
                                    @endforeach
                                </div>
                            @else
                                <div class="products-list-catalog">
                                    @foreach($products as $product)
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
                                        <div class="product-list-card" wire:key="list-p-{{ $product->id }}">
                                            <!-- Image wrapper -->
                                            <a href="{{ route('products.show', $product->slug) }}" class="list-image-wrapper" style="position: relative; width: 220px; min-width: 220px; aspect-ratio: 1; overflow: hidden; background: var(--gray-100); display: block;">
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" class="product-image" loading="lazy">
                                                @if ($discountPercentage > 0)
                                                    <span class="product-badge badge-sale" style="position: absolute; top: 12px; left: 12px; padding: 4px 10px; border-radius: var(--radius-md); font-size: var(--text-xs); font-weight: var(--font-semibold); background: var(--danger); color: white;">-{{ $discountPercentage }}%</span>
                                                @elseif ($product->is_featured)
                                                    <span class="product-badge badge-new" style="position: absolute; top: 12px; left: 12px; padding: 4px 10px; border-radius: var(--radius-md); font-size: var(--text-xs); font-weight: var(--font-semibold); background: var(--primary-500); color: white;">New</span>
                                                @endif
                                            </a>
                                            
                                            <!-- Info details -->
                                            <a href="{{ route('products.show', $product->slug) }}" style="flex: 1; text-decoration: none; color: inherit; display: flex;">
                                                <div class="list-details" style="flex: 1; padding: var(--space-6); display: flex; flex-direction: column; text-align: left; height: 100%;">
                                                    <span class="list-category" style="font-size: var(--text-xs); color: var(--text-muted); font-weight: var(--font-medium); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: var(--space-1);">{{ $product->category->name ?? 'Premium' }}</span>
                                                    <h3 class="list-title" style="font-size: var(--text-lg); font-weight: var(--font-semibold); color: var(--text-primary); margin-bottom: var(--space-2); line-height: 1.4;">{{ $product->name }}</h3>
                                                    
                                                    <div class="list-rating" style="display: flex; align-items: center; gap: var(--space-1); margin-top: var(--space-2); margin-bottom: var(--space-4);">
                                                        <div class="stars" style="display: flex; gap: 2px;">
                                                            @for ($i = 0; $i < $fullStars; $i++)
                                                                <svg viewBox="0 0 20 20" style="width: 14px; height: 14px; color: var(--warning); fill: currentColor;"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                            @endfor
                                                            @if ($halfStar)
                                                                <svg viewBox="0 0 20 20" style="width: 14px; height: 14px; color: var(--warning); fill: currentColor;"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                            @endif
                                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                                <svg viewBox="0 0 20 20" class="star-empty" style="width: 14px; height: 14px; color: var(--gray-200); fill: none; stroke: currentColor; stroke-width: 2;"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                            @endfor
                                                        </div>
                                                        <span class="rating-count" style="font-size: var(--text-xs); color: var(--text-muted);">({{ $product->rating_count }})</span>
                                                    </div>
                                                    
                                                    <p class="list-desc" style="font-size: var(--text-sm); color: var(--text-secondary); line-height: 1.6; margin-bottom: auto;">
                                                        {{ $product->short_description ?? Str::limit(strip_tags($product->description), 140) }}
                                                    </p>
                                                </div>
                                            </a>
                                            
                                            <!-- Action side panel -->
                                            <div class="list-actions" style="width: 200px; padding: var(--space-6); border-left: 1px solid var(--border); display: flex; flex-direction: column; justify-content: center; align-items: stretch; gap: var(--space-4);">
                                                <div class="list-price-box" style="display: flex; flex-direction: column; gap: var(--space-1); text-align: center;">
                                                    <span class="list-price" style="font-size: var(--text-xl); font-weight: var(--font-bold); color: var(--text-primary);">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                                    @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                                                        <span class="list-old-price" style="font-size: var(--text-sm); color: var(--text-muted); text-decoration: line-through;">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                                @php
                                                    $hasVariants = $product->variants()->where('is_active', true)->exists();
                                                @endphp
                                                @if (request()->routeIs('catalog') && !$hasVariants)
                                                    <button class="btn btn-primary btn-add-cart" wire:click.prevent="addToCart({{ $product->id }})" style="font-size: var(--text-sm); padding: 8px 16px;">Add to Cart</button>
                                                @else
                                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary btn-add-cart" style="font-size: var(--text-sm); padding: 8px 16px;">Add to Cart</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Pagination Section matching mockup markup structure -->
                            @if ($products->hasPages())
                                <div class="pagination-wrap">
                                    {{-- Prev page --}}
                                    @if ($products->onFirstPage())
                                        <button class="page-btn disabled" aria-label="Previous page">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        </button>
                                    @else
                                        <button class="page-btn" wire:click="previousPage" aria-label="Previous page">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        </button>
                                    @endif
                                    
                                    {{-- Page numbers --}}
                                    @for ($page = 1; $page <= $products->lastPage(); $page++)
                                        @if ($page == $products->currentPage())
                                            <button class="page-btn active">{{ $page }}</button>
                                        @else
                                            <button class="page-btn" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                                        @endif
                                    @endfor
                                    
                                    {{-- Next page --}}
                                    @if ($products->hasMorePages())
                                        <button class="page-btn" wire:click="nextPage" aria-label="Next page">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    @else
                                        <button class="page-btn disabled" aria-label="Next page">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
