<?php

namespace App\Billing\Http\Controllers;

use App\Billing\Services\PayPalBillingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController
{
    public function __construct(
        private readonly PayPalBillingService $paypal,
    ) {}

    public function __invoke(Request $request): Response
    {
        if (! $this->paypal->isConfigured()) {
            return response('PayPal billing is not configured.', 503);
        }

        $payload = $request->all();

        try {
            $this->paypal->handleWebhookEvent($payload);
        } catch (\Throwable $exception) {
            Log::warning('PayPal webhook processing failed', [
                'message' => $exception->getMessage(),
                'event_type' => $payload['event_type'] ?? null,
            ]);

            return response('Webhook processing failed.', 500);
        }

        return response('Webhook handled.', 200);
    }
}
