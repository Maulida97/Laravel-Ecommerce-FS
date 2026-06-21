<div>
    @section('title', 'My Wishlist — Tokoku.id')

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <h1 class="font-bold text-3xl text-text-primary mb-2">Your Wishlist</h1>
            <p class="text-base text-text-secondary">
                @if($wishlistItems->isEmpty())
                    You don't have any saved items yet.
                @else
                    You have {{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }} saved for later.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <label for="sortBy" class="text-xs font-semibold text-text-muted uppercase tracking-wider font-label-caps">Sort By:</label>
            <select id="sortBy" wire:model.live="sortBy" class="form-input" style="height: 40px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary); font-size: var(--text-sm); font-weight: var(--font-medium); cursor: pointer; outline: none;">
                <option value="newest">Recently Added</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
            </select>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <div class="alert-success" style="background: var(--success); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert-danger" style="background: var(--danger); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
            {{ session('error') }}
        </div>
    @endif

    <!-- Content -->
    @if($wishlistItems->isEmpty())
        <div class="card text-center" style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-16) var(--space-6); color: var(--text-secondary); display: flex; flex-direction: column; align-items: center; gap: var(--space-4);">
            <div style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center;">
                <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">favorite</span>
            </div>
            <div class="max-w-md mx-auto" style="display: flex; flex-direction: column; align-items: center; gap: var(--space-2);">
                <h3 class="font-bold text-xl text-text-primary">Your wishlist is empty</h3>
                <p class="text-sm text-text-secondary" style="margin-bottom: var(--space-4);">Explore our products and tap the heart icon to save products to your wishlist.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary inline-flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Browse Catalog</span>
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($wishlistItems as $item)
                @if($item->product)
                    <x-product-card :product="$item->product" wire:key="wishlist-p-{{ $item->product->id }}" />
                @endif
            @endforeach
        </div>
    @endif
</div>
