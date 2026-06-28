<?php

namespace App\Billing\Services;

use App\Billing\Enums\BillingProvider;
use App\Billing\Models\AccessGrant;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalBillingService
{
    public function isConfigured(): bool
    {
        if (! config('billing.paypal.enabled')) {
            return false;
        }

        foreach (['client_id', 'client_secret', 'plan_id'] as $key) {
            $value = config("billing.paypal.{$key}");

            if (! is_string($value) || $value === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{subscription_id: string, approval_url: string}
     */
    public function createSubscription(User $user, AccessGrant $grant): array
    {
        $response = $this->client()
            ->post('/v1/billing/subscriptions', [
                'plan_id' => config('billing.paypal.plan_id'),
                'custom_id' => (string) $grant->id,
                'subscriber' => [
                    'email_address' => $user->email,
                    'name' => [
                        'given_name' => $user->name,
                    ],
                ],
                'application_context' => [
                    'brand_name' => (string) config('app.name'),
                    'locale' => 'en-US',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    ],
                    'return_url' => route('billing.paypal.success'),
                    'cancel_url' => route('billing.subscribe'),
                ],
            ])
            ->throw()
            ->json();

        $subscriptionId = (string) ($response['id'] ?? '');
        $approvalUrl = collect($response['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if ($subscriptionId === '' || ! is_string($approvalUrl) || $approvalUrl === '') {
            throw new RuntimeException('PayPal did not return a subscription approval URL.');
        }

        $grant->update(['external_id' => $subscriptionId]);

        return [
            'subscription_id' => $subscriptionId,
            'approval_url' => $approvalUrl,
        ];
    }

    public function fetchSubscription(string $subscriptionId): array
    {
        return $this->client()
            ->get("/v1/billing/subscriptions/{$subscriptionId}")
            ->throw()
            ->json();
    }

    public function syncGrantFromSubscription(AccessGrant $grant, array $subscription): AccessGrant
    {
        $status = strtoupper((string) ($subscription['status'] ?? ''));

        return match ($status) {
            'ACTIVE' => app(AccessGrantService::class)->activate($grant),
            'CANCELLED', 'SUSPENDED' => tap($grant, fn (AccessGrant $g) => app(AccessGrantService::class)->markCancelled($g))->refresh(),
            'EXPIRED' => tap($grant, fn (AccessGrant $g) => app(AccessGrantService::class)->markExpired($g))->refresh(),
            default => $grant,
        };
    }

    public function handleWebhookEvent(array $event): void
    {
        $eventType = (string) ($event['event_type'] ?? '');
        $resource = $event['resource'] ?? [];
        $subscriptionId = (string) ($resource['id'] ?? '');

        if ($subscriptionId === '') {
            return;
        }

        $grant = app(AccessGrantService::class)->findByProviderReference(
            BillingProvider::PayPal,
            $subscriptionId,
        );

        if ($grant === null) {
            return;
        }

        if (in_array($eventType, [
            'BILLING.SUBSCRIPTION.ACTIVATED',
            'BILLING.SUBSCRIPTION.RE-ACTIVATED',
        ], true)) {
            app(AccessGrantService::class)->activate($grant);

            return;
        }

        if (in_array($eventType, [
            'BILLING.SUBSCRIPTION.CANCELLED',
            'BILLING.SUBSCRIPTION.SUSPENDED',
        ], true)) {
            app(AccessGrantService::class)->markCancelled($grant);

            return;
        }

        if ($eventType === 'BILLING.SUBSCRIPTION.EXPIRED') {
            app(AccessGrantService::class)->markExpired($grant);
        }
    }

    private function client(): PendingRequest
    {
        $token = $this->accessToken();

        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->asJson()
            ->withToken($token);
    }

    private function accessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(
                (string) config('billing.paypal.client_id'),
                (string) config('billing.paypal.client_secret'),
            )
            ->post($this->baseUrl().'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ])
            ->throw()
            ->json();

        $token = $response['access_token'] ?? null;

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Unable to authenticate with PayPal.');
        }

        return $token;
    }

    private function baseUrl(): string
    {
        return config('billing.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
