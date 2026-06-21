<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected bool $isDummy = true;

    public function __construct()
    {
        $serverKey = config('midtrans.server_key');
        
        if ($serverKey && !str_contains($serverKey, 'dummy') && !str_contains($serverKey, 'xxxxxxxxxxxxxxxx')) {
            $this->isDummy = false;
            Config::$serverKey = $serverKey;
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');
        }
    }

    /**
     * Create Snap Token for an Order.
     */
    public function createSnapToken(Order $order): string
    {
        if ($this->isDummy) {
            Log::info("Midtrans Service: Dummy Snap Token generated for order: " . $order->order_number);
            return 'dummy-snap-token-' . uniqid();
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->guest_name ?? ($order->user ? $order->user->name : 'Customer'),
                    'email' => $order->guest_email ?? ($order->user ? $order->user->email : ''),
                    'phone' => $order->guest_phone ?? ($order->user ? $order->user->phone : ''),
                ],
            ];

            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('Midtrans Snap Token generation error: ' . $e->getMessage());
            // Fallback to dummy in case of API failure
            return 'dummy-snap-token-fallback-' . uniqid();
        }
    }

    /**
     * Verify Midtrans notification signature key.
     */
    public function verifyNotification(array $payload): bool
    {
        if ($this->isDummy) {
            return true;
        }

        try {
            $signatureKey = $payload['signature_key'] ?? '';
            $orderId = $payload['order_id'] ?? '';
            $statusCode = $payload['status_code'] ?? '';
            $grossAmount = $payload['gross_amount'] ?? '';
            
            $expectedSignature = hash('sha512', 
                $orderId . $statusCode . $grossAmount . config('midtrans.server_key')
            );

            return hash_equals($expectedSignature, $signatureKey);
        } catch (Exception $e) {
            Log::error('Midtrans notification verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get transaction status.
     */
    public function getTransactionStatus(string $orderId): array
    {
        if ($this->isDummy) {
            return [
                'transaction_status' => 'settlement',
                'payment_type' => 'qris',
            ];
        }

        try {
            $status = Transaction::status($orderId);
            return (array) $status;
        } catch (Exception $e) {
            Log::error('Midtrans get transaction status error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cancel transaction.
     */
    public function cancelTransaction(string $orderId): bool
    {
        if ($this->isDummy) {
            return true;
        }

        try {
            $result = Transaction::cancel($orderId);
            return $result === '200' || $result === 'ok';
        } catch (Exception $e) {
            Log::error('Midtrans cancel transaction error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refund transaction.
     */
    public function refundTransaction(string $orderId, float $amount): bool
    {
        if ($this->isDummy) {
            return true;
        }

        try {
            $params = ['refund_key' => 'refund_' . time(), 'amount' => (int) $amount, 'reason' => 'Customer request'];
            $result = Transaction::refund($orderId, $params);
            return $result === '200' || $result === 'ok';
        } catch (Exception $e) {
            Log::error('Midtrans refund transaction error: ' . $e->getMessage());
            return false;
        }
    }
}
