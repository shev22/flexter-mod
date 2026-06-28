<?php

namespace App\Billing\Enums;

enum BillingProvider: string
{
    case Stripe = 'stripe';
    case PayPal = 'paypal';
    case Prepaid = 'prepaid';

    public function label(): string
    {
        return match ($this) {
            self::Stripe => 'Card & wallets',
            self::PayPal => 'PayPal',
            self::Prepaid => 'Access code',
        };
    }
}
