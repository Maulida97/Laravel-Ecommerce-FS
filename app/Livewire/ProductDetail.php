<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use App\Models\VariantAttribute;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;
    public $selectedAttributes = [];
    public $quantity = 1;
    public $activeImage = '';
    public $productAttributes;

    /**
     * Mount component and setup initial state.
     */
    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'images', 'variants.combinations.attribute']);
        $this->activeImage = $product->primary_image_url;

        // Fetch attributes and values that are active for this product's variants
        $this->productAttributes = VariantAttribute::whereHas('values.variants', function ($q) {
            $q->where('product_id', $this->product->id)->where('product_variants.is_active', true);
        })->with(['values' => function ($q) {
            $q->whereHas('variants', function ($v) {
                $v->where('product_id', $this->product->id)->where('product_variants.is_active', true);
            })->orderBy('value');
        }])->get();

        // Pre-select combination of first active variant
        $firstVariant = $this->product->variants()->where('is_active', true)->first();
        if ($firstVariant) {
            foreach ($firstVariant->combinations as $val) {
                $this->selectedAttributes[$val->variant_attribute_id] = $val->id;
            }
        }
    }

    /**
     * Select a variant attribute value.
     */
    public function selectAttribute($attributeId, $valueId)
    {
        $this->selectedAttributes[$attributeId] = $valueId;
        
        // Reset quantity if it exceeds the new stock level
        $stock = $this->stock;
        if ($this->quantity > $stock) {
            $this->quantity = $stock > 0 ? 1 : 0;
        }
    }

    /**
     * Set active display image.
     */
    public function setImage($url)
    {
        $this->activeImage = $url;
    }

    /**
     * Increment purchase quantity.
     */
    public function increment()
    {
        $maxStock = $this->stock;
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    /**
     * Decrement purchase quantity.
     */
    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Get active selected variant.
     */
    public function getVariantProperty()
    {
        if ($this->product->variants->isEmpty()) {
            return null;
        }

        $selectedValIds = array_values(array_filter($this->selectedAttributes));
        
        if (count($selectedValIds) < $this->productAttributes->count()) {
            return null;
        }

        return $this->product->variants()
            ->where('is_active', true)
            ->where(function ($query) use ($selectedValIds) {
                foreach ($selectedValIds as $valId) {
                    $query->whereHas('combinations', function ($q) use ($valId) {
                        $q->where('variant_attribute_values.id', $valId);
                    });
                }
            })->first();
    }

    /**
     * Get active variant or product stock level.
     */
    public function getStockProperty()
    {
        if ($this->product->variants->isNotEmpty()) {
            $variant = $this->variant;
            return $variant ? $variant->stock_quantity : 0;
        }
        return $this->product->stock_quantity;
    }

    /**
     * Get active price (with variant adjustment).
     */
    public function getPriceProperty()
    {
        if ($this->product->variants->isNotEmpty()) {
            $variant = $this->variant;
            return $variant ? ($this->product->price + $variant->price_adjustment) : $this->product->price;
        }
        return $this->product->price;
    }

    /**
     * Get or create active shopping cart.
     */
    protected function getOrCreateCart()
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        
        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Add selected product / variant to cart.
     */
    public function addToCart()
    {
        $stock = $this->stock;
        if ($stock <= 0) {
            session()->flash('error', 'This product combination is out of stock.');
            return;
        }

        if ($this->product->variants->isNotEmpty() && !$this->variant) {
            session()->flash('error', 'Please select all product options.');
            return;
        }

        if ($this->quantity < 1 || $this->quantity > $stock) {
            session()->flash('error', 'Invalid quantity selected.');
            return;
        }

        $cart = $this->getOrCreateCart();
        $variant = $this->variant;
        $variantId = $variant ? $variant->id : null;

        $cartItem = $cart->items()
            ->where('product_id', $this->product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        $newQty = $this->quantity;
        if ($cartItem) {
            $newQty += $cartItem->quantity;
        }

        if ($newQty > $stock) {
            session()->flash('error', 'Cannot add more items. Total exceeds available stock.');
            return;
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'unit_price' => $this->price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $this->product->id,
                'product_variant_id' => $variantId,
                'quantity' => $this->quantity,
                'unit_price' => $this->price,
            ]);
        }

        $this->quantity = 1;
        $this->dispatch('cart-updated');
        session()->flash('success', 'Product added to cart successfully!');
    }

    /**
     * Render view.
     */
    public function render()
    {
        $relatedProducts = Product::active()
            ->with(['category', 'primaryImage', 'images'])
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->limit(4)
            ->get();

        return view('livewire.product-detail', compact('relatedProducts'))
            ->layout('layouts.app');
    }
}
