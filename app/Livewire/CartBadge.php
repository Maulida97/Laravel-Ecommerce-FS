<?php

namespace App\Livewire;

use App\Models\Cart;
use Livewire\Component;
use Livewire\Attributes\On;

class CartBadge extends Component
{
    #[On('cart-updated')]
    public function refresh()
    {
        // Dynamic re-render
    }

    public function removeItem($itemId)
    {
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        $count = 0;
        $cart = null;
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->with('items.product.images')->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->with('items.product.images')->first();
        }

        if ($cart) {
            $count = $cart->getItemCount();
        }

        return view('livewire.cart-badge', compact('count', 'cart'));
    }
}
