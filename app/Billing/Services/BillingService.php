<?php

namespace App\Billing\Services;

use App\Billing\Services\Interfaces\BillingServiceInterface;
use App\Models\User;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;

class BillingService implements BillingServiceInterface
{
    public function __construct(
        private readonly SiteSettingsServiceInterface $siteSettings,
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

        return $user->subscribed('default');
    }

    public function sharedState(?User $user): array
    {
        $enabled = $this->isPaymentsEnabled();
        $subscribed = $user?->subscribed('default') ?? false;

        return [
            'enabled' => $enabled,
            'required' => $enabled,
            'can_play' => $this->userCanPlay($user),
            'subscribed' => $subscribed,
            'on_trial' => $user?->onTrial('default') ?? false,
            'plan' => [
                'name' => (string) config('billing.plan.name'),
                'description' => (string) config('billing.plan.description'),
                'amount' => (int) config('billing.plan.amount_cents') / 100,
                'currency' => strtoupper((string) config('billing.plan.currency')),
                'interval' => (string) config('billing.plan.interval'),
            ],
        ];
    }
}
