<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Catalog extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'categories')]
    public $selectedCategories = [];

    #[Url(as: 'min_price')]
    public $minPrice = null;

    #[Url(as: 'max_price')]
    public $maxPrice = null;

    #[Url(as: 'sort')]
    public $sortBy = 'newest';

    #[Url(as: 'layout')]
    public $layout = 'grid';

    public $dbMinPrice = 0;
    public $dbMaxPrice = 10000000;
    public $priceStep = 10000;

    /**
     * Initialize catalog constraints.
     */
    public function mount()
    {
        $prices = Product::active()->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        
        $this->dbMinPrice = floor($prices->min_price ?? 0);
        $this->dbMaxPrice = ceil($prices->max_price ?? 10000000);

        $range = $this->dbMaxPrice - $this->dbMinPrice;
        if ($range <= 0) {
            $this->priceStep = 1;
        } elseif ($range <= 100) {
            $this->priceStep = 1;
        } elseif ($range <= 1000) {
            $this->priceStep = 10;
        } elseif ($range <= 10000) {
            $this->priceStep = 100;
        } elseif ($range <= 100000) {
            $this->priceStep = 1000;
        } else {
            $this->priceStep = 10000;
        }

        if (null === $this->minPrice) {
            $this->minPrice = $this->dbMinPrice;
        }

        if (null === $this->maxPrice) {
            $this->maxPrice = $this->dbMaxPrice;
        }
    }

    /**
     * Reset pagination when filters change.
     */
    public function updating($property)
    {
        if (in_array($property, ['search', 'selectedCategories', 'minPrice', 'maxPrice', 'sortBy'])) {
            $this->resetPage();
        }
    }

    /**
     * Clear all filters.
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategories = [];
        $this->minPrice = $this->dbMinPrice;
        $this->maxPrice = $this->dbMaxPrice;
        $this->sortBy = 'newest';
        $this->resetPage();
    }

    /**
     * Toggle layout grid/list.
     */
    public function toggleLayout($type)
    {
        if (in_array($type, ['grid', 'list'])) {
            $this->layout = $type;
        }
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

        $cartService = app(\App\Services\CartService::class);
        $cartService->addItem($productId, null, 1);

        $this->dispatch('cart-updated');
        session()->flash('success', $product->name . ' added to cart successfully!');
    }

    /**
     * Render catalog view with filtered data.
     */
    public function render()
    {
        $query = Product::active()
            ->with(['category', 'primaryImage', 'images']);

        // Search Filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Category Filter
        if (!empty($this->selectedCategories)) {
            $query->whereHas('category', function ($q) {
                $q->whereIn('slug', $this->selectedCategories);
            });
        }

        // Price Filter
        $min = (is_numeric($this->minPrice) && $this->minPrice !== '') ? (float)$this->minPrice : $this->dbMinPrice;
        $max = (is_numeric($this->maxPrice) && $this->maxPrice !== '') ? (float)$this->maxPrice : $this->dbMaxPrice;
        
        if ($min > $max) {
            $temp = $min;
            $min = $max;
            $max = $temp;
        }

        $query->whereBetween('price', [$min, $max]);

        // Sorting
        switch ($this->sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('is_featured', 'desc')->orderBy('id', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(12);

        // Fetch all active categories with product counts
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        return view('livewire.catalog', compact('products', 'categories'))
            ->layout('layouts.app');
    }
}
