<?php

return [
    'plan' => [
        'name' => env('BILLING_PLAN_NAME', 'Flexter Premium'),
        'description' => env(
            'BILLING_PLAN_DESCRIPTION',
            'Stream unlimited movies and series. Cancel anytime.',
        ),
        'amount_cents' => (int) env('BILLING_AMOUNT_CENTS', 199),
        'currency' => env('BILLING_CURRENCY', 'usd'),
        'interval' => env('BILLING_INTERVAL', 'month'),
    ],

    'stripe' => [
        'price_id' => env('STRIPE_PRICE_ID'),
        'enabled' => env('BILLING_STRIPE_ENABLED', true),
        'automatic_payment_methods' => env('STRIPE_AUTOMATIC_PAYMENT_METHODS', true),
        'payment_method_types' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('STRIPE_PAYMENT_METHOD_TYPES', 'card,link')),
        ))),
    ],

    'paypal' => [
        'enabled' => env('BILLING_PAYPAL_ENABLED', false),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'plan_id' => env('PAYPAL_PLAN_ID'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],

    'prepaid' => [
        'enabled' => env('BILLING_PREPAID_ENABLED', true),
        'label' => env('BILLING_PREPAID_LABEL', 'Access code'),
        'description' => env(
            'BILLING_PREPAID_DESCRIPTION',
            'Redeem a prepaid or crypto access code for premium streaming.',
        ),
    ],
];
