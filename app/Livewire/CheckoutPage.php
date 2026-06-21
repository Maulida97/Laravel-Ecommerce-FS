<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Services\CheckoutService;
use Livewire\Component;

class CheckoutPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount()
    {
        $cart = app(CartService::class)->getCart();
        if ($cart->items->isEmpty()) {
            return $this->redirect(route('cart'), navigate: true);
        }

        // Pre-fill user data if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name ?? '';
            $this->email = $user->email ?? '';

            $defaultAddress = $user->addresses()->where('is_default', true)->first();
            if ($defaultAddress) {
                $this->phone = $defaultAddress->phone;
                $this->address = $defaultAddress->address_line;
                $this->city = $defaultAddress->city;
                $this->postal_code = $defaultAddress->postal_code;
            } else {
                $this->phone = $user->phone ?? '';
                $this->address = $user->address ?? '';
            }
        }
    }

    /**
     * Select a saved address to auto-populate the shipping form.
     */
    public function selectAddress($addressId)
    {
        if (!auth()->check()) return;

        $address = auth()->user()->addresses()->find($addressId);
        if ($address) {
            $this->name = $address->recipient_name;
            $this->phone = $address->phone;
            $this->address = $address->address_line;
            $this->city = $address->city;
            $this->postal_code = $address->postal_code;
        }
    }

    public function placeOrder()
    {
        $validatedData = $this->validate();

        $cartService = app(CartService::class);
        $cart = $cartService->getCart();

        if ($cart->items->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        try {
            $checkoutService = app(CheckoutService::class);
            
            $subtotal = $cart->getSubtotal();
            $shippingCost = ($subtotal > 0 && $subtotal < 500000) ? 15000 : 0;
            $total = $subtotal + $shippingCost;

            $order = $checkoutService->createOrder([
                'cart' => $cart,
                'shipping' => $validatedData,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
            ]);

            $snapToken = $checkoutService->processPayment($order);

            $this->dispatch('cart-updated'); // Reset navbar cart count

            // Dispatch browser event to invoke Midtrans Snap on the frontend
            $this->dispatch('initiate-payment', [
                'snapToken' => $snapToken,
                'orderNumber' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $cartService = app(CartService::class);
        $cart = $cartService->getCart()->load(['items.product.images', 'items.variant']);

        $subtotal = $cart->getSubtotal();
        $shippingCost = ($subtotal > 0 && $subtotal < 500000) ? 15000 : 0;
        $total = $subtotal + $shippingCost;

        $savedAddresses = collect();
        if (auth()->check()) {
            $savedAddresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
        }

        return view('livewire.checkout-page', compact('cart', 'subtotal', 'shippingCost', 'total', 'savedAddresses'))
            ->layout('layouts.app');
    }
}
