<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected MidtransService $midtrans;
    protected CheckoutService $checkout;

    public function __construct(MidtransService $midtrans, CheckoutService $checkout)
    {
        $this->midtrans = $midtrans;
        $this->checkout = $checkout;
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        
        Log::info('Midtrans Webhook Payload: ', $payload);

        // Verify notification signature
        if (!$this->midtrans->verifyNotification($payload)) {
            Log::warning('Midtrans Webhook: Invalid signature key');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        try {
            $this->checkout->handlePaymentNotification($payload);
            return response()->json(['message' => 'Notification processed successfully']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook processing error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
