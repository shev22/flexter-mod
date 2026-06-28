<?php

namespace App\Billing\Services;

use App\Billing\Enums\BillingProvider;
use App\Billing\Models\AccessGrant;
use App\Billing\Models\PrepaidAccessCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PrepaidCodeService
{
    public function isEnabled(): bool
    {
        return (bool) config('billing.prepaid.enabled');
    }

    public function redeem(User $user, string $rawCode): AccessGrant
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Access code redemption is not enabled.');
        }

        $code = strtoupper(trim($rawCode));

        return DB::transaction(function () use ($user, $code) {
            /** @var PrepaidAccessCode|null $record */
            $record = PrepaidAccessCode::query()
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if ($record === null) {
                throw new RuntimeException('That access code is invalid.');
            }

            if ($record->isRedeemed()) {
                throw new RuntimeException('That access code has already been used.');
            }

            $record->update([
                'redeemed_by' => $user->id,
                'redeemed_at' => now(),
            ]);

            return app(AccessGrantService::class)->grantFixedTerm(
                $user,
                BillingProvider::Prepaid,
                $record->duration_days,
                (string) $record->id,
            );
        });
    }

    public function generateCode(int $durationDays, ?string $label = null, ?User $creator = null): PrepaidAccessCode
    {
        do {
            $code = strtoupper(implode('-', [
                Str::upper(Str::random(4)),
                Str::upper(Str::random(4)),
                Str::upper(Str::random(4)),
            ]));
        } while (PrepaidAccessCode::query()->where('code', $code)->exists());

        return PrepaidAccessCode::query()->create([
            'code' => $code,
            'duration_days' => max(1, $durationDays),
            'label' => $label,
            'created_by' => $creator?->id,
        ]);
    }
}
