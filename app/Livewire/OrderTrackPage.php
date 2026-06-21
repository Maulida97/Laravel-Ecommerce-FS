<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderTrackPage extends Component
{
    public string $order_number = '';
    public string $email = '';
    public $order = null;
    public string $errorMessage = '';

    public function mount()
    {
        $this->order_number = request()->query('order_number', '');
        $this->email = request()->query('email', '');

        if ($this->order_number) {
            $this->trackOrder();
        }
    }

    public function trackOrder()
    {
        $this->errorMessage = '';
        $this->order = null;

        if (empty($this->order_number)) {
            $this->errorMessage = 'Order number is required.';
            return;
        }

        $query = Order::where('order_number', trim($this->order_number))
            ->with(['items.product', 'statusHistories', 'user']);

        $order = $query->first();

        if (!$order) {
            $this->errorMessage = 'Order not found. Please verify the order number.';
            return;
        }

        // Security check: If order is associated with user, verify logged-in user or email match
        if ($order->user_id) {
            if (!auth()->check() || auth()->id() !== $order->user_id) {
                if (empty($this->email) || strtolower($order->user->email) !== strtolower(trim($this->email))) {
                    $this->errorMessage = 'Please provide the correct email address for this order.';
                    return;
                }
            }
        } else {
            // Guest order: verify using guest email
            if (empty($this->email) || strtolower($order->guest_email) !== strtolower(trim($this->email))) {
                $this->errorMessage = 'Please provide the correct email address for this order.';
                return;
            }
        }

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.order-track-page')
            ->layout('layouts.app');
    }
}
