import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useBilling() {
    const page = usePage();

    const billing = computed(() => page.props.billing ?? {});

    const paymentsEnabled = computed(() => billing.value.enabled === true);
    const subscriptionRequired = computed(() => billing.value.required === true);
    const canPlay = computed(() => billing.value.can_play !== false);
    const isSubscribed = computed(() => billing.value.subscribed === true);
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
        plan,
        formattedPrice,
        playbackBlockedReason,
    };
}
