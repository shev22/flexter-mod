<?php

namespace App\Billing\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrepaidAccessCode extends Model
{
    protected $fillable = [
        'code',
        'duration_days',
        'label',
        'redeemed_by',
        'redeemed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'redeemed_at' => 'datetime',
        ];
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isRedeemed(): bool
    {
        return $this->redeemed_at !== null;
    }
}
