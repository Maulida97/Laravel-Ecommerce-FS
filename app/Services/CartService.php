<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get or create active cart for the current session/user.
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $user = Auth::user();
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        $sessionId = Session::getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Add item to active cart.
     */
    public function addItem(int $productId, ?int $variantId, int $qty): CartItem
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);
        
        $price = $product->price;
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $price += $variant->price_adjustment;
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($item) {
            $item->quantity += $qty;
            $item->unit_price = $price; // Update price
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $qty,
                'unit_price' => $price,
            ]);
        }

        return $item;
    }

    /**
     * Update quantity of an item.
     */
    public function updateItem(int $itemId, int $qty): CartItem
    {
        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);

        if ($qty <= 0) {
            $item->delete();
            return $item;
        }

        // Validate stock
        $maxStock = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
        if ($qty > $maxStock) {
            $qty = $maxStock;
        }

        $item->quantity = $qty;
        $item->save();

        return $item;
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
    }

    /**
     * Clear all items in cart.
     */
    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    /**
     * Get subtotal of cart.
     */
    public function getTotal(): float
    {
        return $this->getCart()->getSubtotal();
    }

    /**
     * Get item count in cart.
     */
    public function getItemCount(): int
    {
        return $this->getCart()->getItemCount();
    }

    /**
     * Merge guest cart into user cart upon login.
     */
    public function mergeGuestCart(int $userId): void
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $guestItem) {
            $userItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($userItem) {
                $totalQty = $userItem->quantity + $guestItem->quantity;
                $maxStock = $userItem->variant ? $userItem->variant->stock_quantity : $userItem->product->stock_quantity;
                $userItem->quantity = min($totalQty, $maxStock);
                $userItem->save();
            } else {
                $guestItem->cart_id = $userCart->id;
                $guestItem->save();
            }
        }

        // Delete guest cart
        $guestCart->delete();
    }
}
