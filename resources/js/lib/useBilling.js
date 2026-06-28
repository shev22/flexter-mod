import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useBilling() {
    const page = usePage();

    const billing = computed(() => page.props.billing ?? {});

    const paymentsEnabled = computed(() => billing.value.enabled === true);
    const subscriptionRequired = computed(() => billing.value.required === true);
    const canPlay = computed(() => billing.value.can_play !== false);
    const isSubscribed = computed(() => billing.value.subscribed === true);
    const stripeConfigured = computed(() => billing.value.stripe_configured === true);
    const paypalConfigured = computed(() => billing.value.paypal_configured === true);
    const prepaidEnabled = computed(() => billing.value.prepaid_enabled === true);
    const checkoutAvailable = computed(() => billing.value.checkout_available === true);
    const provider = computed(() => billing.value.provider ?? null);
    const accessEndsAt = computed(() => billing.value.access_ends_at ?? null);
    const paymentMethods = computed(() => billing.value.payment_methods ?? []);
    const plan = computed(() => billing.value.plan ?? {});

    const formattedPrice = computed(() => {
        const amount = plan.value.amount ?? 1.99;
        const currency = (plan.value.currency ?? 'USD').toUpperCase();

        try {
            return new Intl.NumberFormat(undefined, {
                style: 'currency',
                currency,
            }).format(amount);
        } catch {
            return `$${Number(amount).toFixed(2)}`;
        }
    });

    const subscriptionLabel = computed(() => {
        if (!isSubscribed.value) {
            return null;
        }

        return matchProvider(provider.value);
    });

    function matchProvider(value) {
        switch (value) {
            case 'stripe':
                return 'Stripe subscription';
            case 'paypal':
                return 'PayPal subscription';
            case 'prepaid':
                return 'Prepaid access';
            default:
                return 'Premium access';
        }
    }

    function playbackBlockedReason() {
        if (!paymentsEnabled.value || canPlay.value) {
            return null;
        }

        if (!page.props.auth?.user) {
            return 'Sign in and subscribe to start watching.';
        }

        return 'Subscribe to Flexter Premium to stream movies and series.';
    }

    return {
        billing,
        paymentsEnabled,
        subscriptionRequired,
        canPlay,
        isSubscribed,
        stripeConfigured,
        paypalConfigured,
        prepaidEnabled,
        checkoutAvailable,
        provider,
        accessEndsAt,
        paymentMethods,
        subscriptionLabel,
        plan,
        formattedPrice,
        playbackBlockedReason,
    };
}
