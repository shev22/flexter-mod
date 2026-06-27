<?php

namespace App\Billing\Http\Controllers;

use App\Billing\Services\Interfaces\BillingServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function __construct(
        private readonly BillingServiceInterface $billing,
    ) {}

    public function subscribe(): Response|RedirectResponse
    {
        if (! $this->billing->isPaymentsEnabled()) {
            return redirect()->route('home');
        }

        /** @var User|null $user */
        $user = Auth::user();

        if ($user?->subscribed('default')) {
            return redirect()->route('settings');
        }

        return Inertia::render('Billing/Subscribe', [
            'billing' => $this->billing->sharedState($user),
            'stripeKey' => config('cashier.key'),
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        if (! $this->billing->isPaymentsEnabled()) {
            return redirect()->route('home');
        }

        /** @var User $user */
        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()->route('settings')->with('success', 'You already have an active subscription.');
        }

        $priceId = config('billing.stripe.price_id');

        if (! is_string($priceId) || $priceId === '') {
            return back()->with('error', 'Billing is not configured yet. Please contact support.');
        }

        return $user
            ->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.subscribe'),
            ])
            ->redirect();
    }

    public function success(Request $request): RedirectResponse
    {
        return redirect()
            ->route('settings')
            ->with('success', 'Welcome to Flexter Premium! You can now stream everything.');
    }

    public function portal(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->hasStripeId()) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'No billing account found. Subscribe first to manage your plan.');
        }

        return $user->redirectToBillingPortal(route('settings'));
    }
}
