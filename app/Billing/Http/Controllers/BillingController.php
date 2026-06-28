<?php

namespace App\Billing\Http\Controllers;

use App\Billing\Enums\BillingProvider;
use App\Billing\Services\AccessGrantService;
use App\Billing\Services\Interfaces\BillingServiceInterface;
use App\Billing\Services\PayPalBillingService;
use App\Billing\Services\PrepaidCodeService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function __construct(
        private readonly BillingServiceInterface $billing,
        private readonly PayPalBillingService $paypal,
        private readonly PrepaidCodeService $prepaidCodes,
        private readonly AccessGrantService $accessGrants,
    ) {}

    public function subscribe(): Response|RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user !== null && $this->billing->userHasPremium($user)) {
            return redirect()->route('settings');
        }

        $state = $this->billing->sharedState($user);

        return Inertia::render('Billing/Subscribe', [
            'billing' => $state,
            'stripeKey' => config('cashier.key'),
            'paymentsEnabled' => $this->billing->isPaymentsEnabled(),
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $request->validate([
            'provider' => ['required', Rule::in([
                BillingProvider::Stripe->value,
                BillingProvider::PayPal->value,
            ])],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! $this->billing->isPaymentsEnabled()) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'Premium subscriptions are not enabled yet.');
        }

        if ($this->billing->userHasPremium($user)) {
            return redirect()->route('settings')->with('success', 'You already have active premium access.');
        }

        return match ($request->string('provider')->toString()) {
            BillingProvider::PayPal->value => $this->checkoutPayPal($user),
            default => $this->checkoutStripe($user),
        };
    }

    public function redeem(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! $this->billing->isPaymentsEnabled()) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'Premium subscriptions are not enabled yet.');
        }

        if ($this->billing->userHasPremium($user)) {
            return redirect()->route('settings')->with('success', 'You already have active premium access.');
        }

        try {
            $this->prepaidCodes->redeem($user, $request->string('code')->toString());
        } catch (\Throwable $exception) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('settings')
            ->with('success', 'Access code redeemed. Welcome to Flexter Premium!');
    }

    public function success(Request $request): RedirectResponse
    {
        return redirect()
            ->route('settings')
            ->with('success', 'Welcome to Flexter Premium! You can now stream everything.');
    }

    public function paypalSuccess(Request $request): RedirectResponse
    {
        $subscriptionId = $request->string('subscription_id')->toString();

        if ($subscriptionId === '') {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'PayPal did not return a subscription reference.');
        }

        $grant = $this->accessGrants->findByProviderReference(
            BillingProvider::PayPal,
            $subscriptionId,
        );

        if ($grant === null) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'We could not find your PayPal subscription. Please contact support.');
        }

        try {
            $subscription = $this->paypal->fetchSubscription($subscriptionId);
            $this->paypal->syncGrantFromSubscription($grant, $subscription);
        } catch (\Throwable) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'We could not confirm your PayPal subscription yet. Please try again in a moment.');
        }

        if (! $grant->refresh()->isActive()) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'Your PayPal subscription is not active yet. Please try again in a moment.');
        }

        return redirect()
            ->route('settings')
            ->with('success', 'PayPal subscription active. Welcome to Flexter Premium!');
    }

    public function portal(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasStripeId()) {
            return $user->redirectToBillingPortal(route('settings'));
        }

        return redirect()
            ->route('settings')
            ->with('error', 'Billing is managed through your payment provider. Cancel PayPal from your PayPal account, or wait for prepaid access to expire.');
    }

    private function checkoutStripe(User $user): RedirectResponse
    {
        $state = $this->billing->sharedState($user);

        if (! ($state['stripe_configured'] ?? false)) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'Checkout is temporarily unavailable. Please try again later.');
        }

        $priceId = config('billing.stripe.price_id');

        $checkoutOptions = [
            'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.subscribe'),
        ];

        if (filter_var(config('billing.stripe.automatic_payment_methods'), FILTER_VALIDATE_BOOL)) {
            $checkoutOptions['automatic_payment_methods'] = ['enabled' => true];
        } else {
            $paymentMethodTypes = config('billing.stripe.payment_method_types', ['card']);

            if (is_array($paymentMethodTypes) && $paymentMethodTypes !== []) {
                $checkoutOptions['payment_method_types'] = $paymentMethodTypes;
            }
        }

        return $user
            ->newSubscription('default', $priceId)
            ->checkout($checkoutOptions)
            ->redirect();
    }

    private function checkoutPayPal(User $user): RedirectResponse
    {
        if (! $this->paypal->isConfigured()) {
            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'PayPal checkout is temporarily unavailable. Please try another payment method.');
        }

        $grant = $this->accessGrants->createPending($user, BillingProvider::PayPal);

        try {
            $result = $this->paypal->createSubscription($user, $grant);
        } catch (\Throwable) {
            $this->accessGrants->markCancelled($grant);

            return redirect()
                ->route('billing.subscribe')
                ->with('error', 'PayPal checkout could not be started. Please try again later.');
        }

        return redirect()->away($result['approval_url']);
    }
}
