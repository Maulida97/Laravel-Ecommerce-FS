<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class CheckoutSuccessPage extends Component
{
    public $order;

    public function mount()
    {
        $orderNumber = request()->query('order_id');
        
        $this->order = Order::where('order_number', $orderNumber)
            ->with(['items.product'])
            ->first();

        // If not found, check if it is numeric ID
        if (!$this->order && is_numeric($orderNumber)) {
            $this->order = Order::with(['items.product'])->find($orderNumber);
        }

        // If still not found, redirect to home
        if (!$this->order) {
            return redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.checkout-success-page')
            ->layout('layouts.app');
    }
}
