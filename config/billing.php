<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe credentials
    |--------------------------------------------------------------------------
    |
    | Create a recurring Price in the Stripe Dashboard ($1.99/month) and set
    | STRIPE_PRICE_ID to that price ID (price_...).
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'price_id' => env('STRIPE_PRICE_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription plan
    |--------------------------------------------------------------------------
    */

    'plan' => [
        'name' => env('BILLING_PLAN_NAME', 'Flexter Premium'),
        'description' => env('BILLING_PLAN_DESCRIPTION', 'Unlimited movie & series streaming'),
        'amount_cents' => (int) env('BILLING_AMOUNT_CENTS', 199),
        'currency' => env('BILLING_CURRENCY', 'usd'),
        'interval' => 'month',
    ],

];
