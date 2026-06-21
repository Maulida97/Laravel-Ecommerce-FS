<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckoutService
{
    protected MidtransService $midtrans;
    protected CartService $cartService;

    public function __construct(MidtransService $midtrans, CartService $cartService)
    {
        $this->midtrans = $midtrans;
        $this->cartService = $cartService;
    }

    /**
     * Create an order from the active cart.
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $cart = $data['cart'];
            
            // Generate unique order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Compile shipping details
            $shippingAddress = [
                'name' => $data['shipping']['name'],
                'email' => $data['shipping']['email'],
                'phone' => $data['shipping']['phone'],
                'address' => $data['shipping']['address'],
                'city' => $data['shipping']['city'],
                'postal_code' => $data['shipping']['postal_code'],
            ];

            // Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->check() ? auth()->id() : null,
                'guest_email' => auth()->check() ? null : $data['shipping']['email'],
                'guest_phone' => auth()->check() ? null : $data['shipping']['phone'],
                'guest_name' => auth()->check() ? null : $data['shipping']['name'],
                'shipping_address' => $shippingAddress,
                'billing_address' => $shippingAddress,
                'subtotal' => $data['subtotal'],
                'shipping_cost' => $data['shipping_cost'] ?? 0.00,
                'discount_amount' => $data['discount_amount'] ?? 0.00,
                'tax_amount' => $data['tax_amount'] ?? 0.00,
                'total_amount' => $data['total'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'notes' => $data['shipping']['notes'] ?? null,
            ]);

            // Create Order Items
            foreach ($cart->items as $item) {
                // Stock Check & Reservation
                $this->reserveStock($item->product, $item->variant, $item->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant ? $item->variant->variant_name : null,
                    'sku' => $item->variant ? $item->variant->sku : $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->getTotal(),
                ]);
            }

            // Write Order History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'notes' => 'Order created and waiting for payment.',
            ]);

            // Clear the cart
            $this->cartService->clearCart();

            return $order;
        });
    }

    /**
     * Reserve/Reduce stock of product and variant.
     */
    protected function reserveStock(Product $product, ?ProductVariant $variant, int $qty): void
    {
        if ($variant) {
            if ($variant->stock_quantity < $qty) {
                throw new Exception("Stock not available for variant: " . $variant->variant_name);
            }
            $variant->decrement('stock_quantity', $qty);
        } else {
            if ($product->stock_quantity < $qty) {
                throw new Exception("Stock not available for product: " . $product->name);
            }
            $product->decrement('stock_quantity', $qty);
        }
    }

    /**
     * Restore stock in case of order cancellation / expiration.
     */
    protected function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                $variant = ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    $variant->increment('stock_quantity', $item->quantity);
                }
            } else {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }
        }
    }

    /**
     * Process payment and get snap token.
     */
    public function processPayment(Order $order): string
    {
        return $this->midtrans->createSnapToken($order);
    }

    /**
     * Confirm/Paid order manually or via callback.
     */
    public function confirmOrder(string $orderNumber): Order
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'processing',
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'processing',
                'notes' => 'Payment confirmed and order is now being processed.',
            ]);
        }
        return $order;
    }

    /**
     * Handle payment notification from Midtrans webhook.
     */
    public function handlePaymentNotification(array $payload): void
    {
        $orderNumber = $payload['order_id'] ?? '';
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            Log::error('Order not found: ' . $orderNumber);
            return;
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? '';

        $statusMap = [
            'capture' => 'paid',
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'cancelled',
            'expire' => 'expired',
            'refund' => 'refunded',
        ];

        $newPaymentStatus = $statusMap[$transactionStatus] ?? 'pending';
        $oldPaymentStatus = $order->payment_status;

        $order->update([
            'payment_status' => $newPaymentStatus,
            'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            'midtrans_payment_type' => $paymentType,
            'midtrans_transaction_status' => $transactionStatus,
        ]);

        if ($newPaymentStatus === 'paid' && $oldPaymentStatus !== 'paid') {
            $order->update(['order_status' => 'processing']);
            
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'processing',
                'notes' => "Payment status changed to PAID via Midtrans Webhook (status: {$transactionStatus}, type: {$paymentType})",
            ]);
        } elseif (in_array($newPaymentStatus, ['cancelled', 'expired', 'failed']) && $oldPaymentStatus === 'pending') {
            $order->update(['order_status' => 'cancelled']);
            
            // Restore product stocks
            $this->restoreStock($order);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'notes' => "Order cancelled because payment status changed to {$newPaymentStatus} via Midtrans Webhook",
            ]);
        }
    }
}
