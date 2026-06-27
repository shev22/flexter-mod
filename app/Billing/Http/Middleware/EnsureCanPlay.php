<?php

namespace App\Billing\Http\Middleware;

use App\Billing\Services\Interfaces\BillingServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanPlay
{
    public function __construct(
        private readonly BillingServiceInterface $billing,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->billing->userCanPlay($request->user())) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'An active subscription is required to stream.',
                'code' => 'subscription_required',
            ], 403);
        }

        return redirect()
            ->route('billing.subscribe')
            ->with('error', 'Subscribe to Flexter Premium to start watching.');
    }
}
