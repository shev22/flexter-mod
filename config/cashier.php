<?php

use Laravel\Cashier\Invoices\DompdfInvoiceRenderer;

return [

  'key' => env('STRIPE_KEY'),

  'secret' => env('STRIPE_SECRET'),

  'webhook' => [
      'secret' => env('STRIPE_WEBHOOK_SECRET'),
      'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
  ],

  'currency' => env('CASHIER_CURRENCY', 'usd'),

  'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

  'payment_notification' => env('CASHIER_PAYMENT_NOTIFICATION'),

  'logger' => env('CASHIER_LOGGER'),

  'billable' => env('CASHIER_BILLABLE_MODEL', App\Models\User::class),

  'invoice_renderer' => env('CASHIER_INVOICE_RENDERER', DompdfInvoiceRenderer::class),

];
