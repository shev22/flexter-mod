<?php

namespace App\Billing\Services;

use App\Billing\Enums\BillingProvider;
use App\Billing\Services\Interfaces\BillingServiceInterface;
use App\Models\User;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;

class BillingService implements BillingServiceInterface
{
    public function __construct(
        private readonly SiteSettingsServiceInterface $siteSettings,
        private readonly AccessGrantService $accessGrants,
        private readonly PayPalBillingService $paypal,
        private readonly PrepaidCodeService $prepaidCodes,
    ) {}

    public function isPaymentsEnabled(): bool
    {
        return $this->siteSettings->get()->enablePayments;
    }

    public function requiresSubscription(): bool
    {
        return $this->isPaymentsEnabled();
    }

    public function userCanPlay(?User $user): bool
    {
        if (! $this->requiresSubscription()) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $this->userHasPremium($user);
    }

    public function userHasPremium(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->subscribed('default')) {
            return true;
        }

        return $this->accessGrants->hasActiveGrant($user);
    }

    public function sharedState(?User $user): array
    {
        $enabled = $this->isPaymentsEnabled();
        $subscribed = $this->userHasPremium($user);
        $stripeConfigured = $this->isStripeConfigured();
        $paypalConfigured = $this->paypal->isConfigured();
        $prepaidEnabled = $this->prepaidCodes->isEnabled();
        $activeGrant = $this->accessGrants->activeGrant($user);
        $provider = $this->resolveActiveProvider($user);

        return [
            'enabled' => $enabled,
            'required' => $enabled,
            'can_play' => $this->userCanPlay($user),
            'subscribed' => $subscribed,
            'on_trial' => $user?->onTrial('default') ?? false,
            'stripe_configured' => $stripeConfigured,
            'paypal_configured' => $paypalConfigured,
            'prepaid_enabled' => $prepaidEnabled,
            'checkout_available' => $enabled && ($stripeConfigured || $paypalConfigured || $prepaidEnabled),
            'provider' => $provider?->value,
            'access_ends_at' => $activeGrant?->ends_at?->toIso8601String(),
            'payment_methods' => $this->paymentMethods($stripeConfigured, $paypalConfigured, $prepaidEnabled),
            'plan' => [
                'name' => (string) config('billing.plan.name'),
                'description' => (string) config('billing.plan.description'),
                'amount' => (int) config('billing.plan.amount_cents') / 100,
                'currency' => strtoupper((string) config('billing.plan.currency')),
                'interval' => (string) config('billing.plan.interval'),
            ],
        ];
    }

    public function isStripeConfigured(): bool
    {
        if (! config('billing.stripe.enabled')) {
            return false;
        }

        $priceId = config('billing.stripe.price_id');

        return is_string($priceId) && $priceId !== ''
            && is_string(config('cashier.key')) && config('cashier.key') !== ''
            && is_string(config('cashier.secret')) && config('cashier.secret') !== '';
    }

    /**
     * @return list<array{id: string, label: string, description: string, configured: bool}>
     */
    private function paymentMethods(bool $stripeConfigured, bool $paypalConfigured, bool $prepaidEnabled): array
    {
        $methods = [];

        if (config('billing.stripe.enabled')) {
            $methods[] = [
                'id' => BillingProvider::Stripe->value,
                'label' => BillingProvider::Stripe->label(),
                'description' => 'Card, Apple Pay, Google Pay, and Link via Stripe',
                'configured' => $stripeConfigured,
            ];
        }

        if (config('billing.paypal.enabled')) {
            $methods[] = [
                'id' => BillingProvider::PayPal->value,
                'label' => BillingProvider::PayPal->label(),
                'description' => 'Pay monthly with your PayPal account',
                'configured' => $paypalConfigured,
            ];
        }

        if ($prepaidEnabled) {
            $methods[] = [
                'id' => BillingProvider::Prepaid->value,
                'label' => (string) config('billing.prepaid.label'),
                'description' => (string) config('billing.prepaid.description'),
                'configured' => true,
            ];
        }

        return $methods;
    }

    private function resolveActiveProvider(?User $user): ?BillingProvider
    {
        if ($user?->subscribed('default')) {
            return BillingProvider::Stripe;
        }

        return $this->accessGrants->activeProvider($user);
    }
}
