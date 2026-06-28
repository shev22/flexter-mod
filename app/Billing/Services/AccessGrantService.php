<?php

namespace App\Billing\Services;

use App\Billing\Enums\BillingProvider;
use App\Billing\Models\AccessGrant;
use App\Models\User;
use Illuminate\Support\Carbon;

class AccessGrantService
{
    public function hasActiveGrant(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return AccessGrant::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query): void {
                $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->exists();
    }

    public function activeGrant(?User $user): ?AccessGrant
    {
        if ($user === null) {
            return null;
        }

        return AccessGrant::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query): void {
                $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('starts_at')
            ->first();
    }

    public function activeProvider(?User $user): ?BillingProvider
    {
        $grant = $this->activeGrant($user);

        return $grant?->providerEnum();
    }

    public function createPending(
        User $user,
        BillingProvider $provider,
        ?string $externalId = null,
    ): AccessGrant {
        return AccessGrant::query()->create([
            'user_id' => $user->id,
            'provider' => $provider->value,
            'external_id' => $externalId,
            'status' => 'pending',
        ]);
    }

    public function activate(
        AccessGrant $grant,
        ?Carbon $startsAt = null,
        ?Carbon $endsAt = null,
    ): AccessGrant {
        $grant->update([
            'status' => 'active',
            'starts_at' => $startsAt ?? now(),
            'ends_at' => $endsAt,
        ]);

        return $grant->refresh();
    }

    public function grantFixedTerm(
        User $user,
        BillingProvider $provider,
        int $durationDays,
        ?string $externalId = null,
    ): AccessGrant {
        return AccessGrant::query()->create([
            'user_id' => $user->id,
            'provider' => $provider->value,
            'external_id' => $externalId,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($durationDays),
        ]);
    }

    public function findByProviderReference(
        BillingProvider $provider,
        string $externalId,
    ): ?AccessGrant {
        return AccessGrant::query()
            ->where('provider', $provider->value)
            ->where('external_id', $externalId)
            ->latest('id')
            ->first();
    }

    public function markCancelled(AccessGrant $grant): void
    {
        $grant->update(['status' => 'cancelled']);
    }

    public function markExpired(AccessGrant $grant): void
    {
        $grant->update([
            'status' => 'expired',
            'ends_at' => $grant->ends_at ?? now(),
        ]);
    }
}
