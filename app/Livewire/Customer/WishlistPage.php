<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\WishlistItem;
use App\Models\Product;
use App\Services\CartService;

class WishlistPage extends Component
{
    public string $sortBy = 'newest';

    protected $listeners = ['wishlist-updated' => '$refresh'];

    /**
     * Toggle wishlist item status.
     */
    public function toggleWishlist($productId)
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $item = auth()->user()->wishlistItems()->where('product_id', $productId)->first();

        if ($item) {
            $item->delete();
            session()->flash('success', 'Product removed from wishlist.');
        } else {
            auth()->user()->wishlistItems()->create(['product_id' => $productId]);
            session()->flash('success', 'Product added to wishlist.');
        }

        $this->dispatch('wishlist-updated');
    }

    /**
     * Add product to cart directly (for products without variants).
     */
    public function addToCart($productId)
    {
        $product = Product::active()->find($productId);
        if (!$product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        // If product has active variants, redirect to product details
        if ($product->variants()->where('is_active', true)->exists()) {
            return redirect()->route('products.show', $product->slug);
        }

        // Check stock
        if ($product->stock_quantity <= 0) {
            session()->flash('error', 'This product is out of stock.');
            return;
        }

        $cartService = app(CartService::class);
        $cartService->addItem($productId, null, 1);

        $this->dispatch('cart-updated');
        session()->flash('success', $product->name . ' added to cart successfully!');
    }

    public function render()
    {
        if (auth()->user()->isAdmin()) {
            return $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
        }

        $query = auth()->user()->wishlistItems()
            ->with(['product.category', 'product.primaryImage', 'product.images']);

        $wishlistItems = $query->get();

        // Sort items
        if ($this->sortBy === 'price_asc') {
            $wishlistItems = $wishlistItems->sortBy(function($item) {
                return $item->product?->price ?? 0;
            });
        } elseif ($this->sortBy === 'price_desc') {
            $wishlistItems = $wishlistItems->sortByDesc(function($item) {
                return $item->product?->price ?? 0;
            });
        } else {
            // Newest added first
            $wishlistItems = $wishlistItems->sortByDesc('created_at');
        }

        return view('livewire.customer.wishlist-page', compact('wishlistItems'))
            ->layout('layouts.customer-portal');
    }
}
