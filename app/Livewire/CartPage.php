<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Services\CartService;
use Livewire\Component;

class CartPage extends Component
{
    public function incrementItem($itemId)
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart();
        $item = $cart->items()->find($itemId);
        if ($item) {
            $cartService->updateItem($itemId, $item->quantity + 1);
            $this->dispatch('cart-updated');
        }
    }

    public function decrementItem($itemId)
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart();
        $item = $cart->items()->find($itemId);
        if ($item) {
            $cartService->updateItem($itemId, $item->quantity - 1);
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem($itemId)
    {
        $cartService = app(CartService::class);
        $cartService->removeItem($itemId);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Item removed from cart.');
    }

    public function clearCart()
    {
        $cartService = app(CartService::class);
        $cartService->clearCart();
        $this->dispatch('cart-updated');
        session()->flash('success', 'Cart cleared successfully.');
    }

    public function render()
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart()->load(['items.product.images', 'items.variant']);
        
        $subtotal = $cart->getSubtotal();
        // Shipping cost: free over 500k, else flat 15k
        $shippingCost = ($subtotal > 0 && $subtotal < 500000) ? 15000 : 0;
        $total = $subtotal + $shippingCost;

        return view('livewire.cart-page', compact('cart', 'subtotal', 'shippingCost', 'total'))
            ->layout('layouts.app');
    }
}
