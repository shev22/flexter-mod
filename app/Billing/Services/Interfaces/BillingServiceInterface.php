<?php

namespace App\Billing\Services\Interfaces;

use App\Models\User;

interface BillingServiceInterface
{
    public function isPaymentsEnabled(): bool;

    public function requiresSubscription(): bool;

    public function userCanPlay(?User $user): bool;

    public function userHasPremium(?User $user): bool;

    public function isStripeConfigured(): bool;

    /**
     * @return array<string, mixed>
     */
    public function sharedState(?User $user): array;
}
