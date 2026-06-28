<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    CheckIcon,
    CreditCardIcon,
    ExclamationTriangleIcon,
    KeyIcon,
    PlayCircleIcon,
    SparklesIcon,
} from '@heroicons/vue/24/solid';
import AppButton from '../../Components/ui/AppButton.vue';
import { useBilling } from '../../lib/useBilling.js';

const props = defineProps({
    billing: { type: Object, required: true },
    stripeKey: { type: String, default: null },
    paymentsEnabled: { type: Boolean, default: true },
});

const page = usePage();
const {
    plan,
    formattedPrice,
    paymentsEnabled: billingPaymentsEnabled,
    paymentMethods,
    checkoutAvailable,
} = useBilling();

const checkout = useForm({ provider: 'stripe' });
const redeem = useForm({ code: '' });

const selectedMethod = ref(paymentMethods.value.find((method) => method.configured)?.id ?? paymentMethods.value[0]?.id ?? 'stripe');
const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);

const selectedMethodMeta = computed(() =>
    paymentMethods.value.find((method) => method.id === selectedMethod.value) ?? null,
);

const canCheckoutSelected = computed(() => {
    if (!props.paymentsEnabled || !billingPaymentsEnabled.value || !page.props.auth?.user) {
        return false;
    }

    if (selectedMethod.value === 'prepaid') {
        return redeem.code.trim().length >= 8;
    }

    return selectedMethodMeta.value?.configured === true;
});

const showUnavailableBanner = computed(
    () => props.paymentsEnabled && billingPaymentsEnabled.value && !checkoutAvailable.value,
);

const perks = [
    'Unlimited movie & series streaming',
    'Continue watching across devices',
    'Full watch history sync',
    'Cancel anytime from your account',
];

function startCheckout() {
    if (!canCheckoutSelected.value) {
        return;
    }

    if (selectedMethod.value === 'prepaid') {
        redeem.post(route('billing.redeem'));
        return;
    }

    checkout.provider = selectedMethod.value;
    checkout.post(route('billing.checkout'));
}

function methodIcon(id) {
    if (id === 'prepaid') {
        return KeyIcon;
    }

    if (id === 'paypal') {
        return CreditCardIcon;
    }

    return CreditCardIcon;
}
</script>

<template>
    <Head title="Subscribe" />

    <div class="mx-auto max-w-3xl px-4 py-12 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl glass-strong p-8 sm:p-12">
            <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-accent/20 blur-3xl" />
            <div class="pointer-events-none absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-accent2/15 blur-3xl" />

            <div class="relative text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-aurora px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-glow">
                    <SparklesIcon class="h-4 w-4" />
                    {{ plan.name || 'Flexter Premium' }}
                </span>

                <h1 class="mt-6 font-display text-4xl font-extrabold text-ink sm:text-5xl">
                    Stream everything for {{ formattedPrice }}/month
                </h1>

                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-muted">
                    {{ plan.description || 'Watch unlimited movies and series. Browse, save to your watchlist, and join discussions — streaming requires a subscription.' }}
                </p>

                <div
                    v-if="!paymentsEnabled || !billingPaymentsEnabled"
                    class="mx-auto mt-6 max-w-md rounded-2xl border border-amber-500/40 bg-amber-500/15 px-4 py-3 text-left text-sm text-amber-900 dark:text-amber-200"
                >
                    <p class="font-semibold text-amber-950 dark:text-amber-100">Subscriptions are not available yet</p>
                    <p class="mt-1 text-amber-900/80 dark:text-amber-200/90">Please check back soon — premium streaming is coming shortly.</p>
                </div>

                <div
                    v-else-if="showUnavailableBanner"
                    class="mx-auto mt-6 max-w-md rounded-2xl border border-amber-500/40 bg-amber-500/15 px-4 py-3 text-left text-sm"
                >
                    <div class="flex items-start gap-3">
                        <ExclamationTriangleIcon class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" />
                        <div>
                            <p class="font-semibold text-amber-950 dark:text-amber-100">Checkout is temporarily unavailable</p>
                            <p class="mt-1 text-amber-900/80 dark:text-amber-200/90">
                                We're finishing payment setup. Please try again later or contact support if this persists.
                            </p>
                            <p v-if="isAdmin" class="mt-2 text-xs text-amber-900/70 dark:text-amber-200/70">
                                Admin: configure Stripe/PayPal in your server <code class="rounded bg-black/10 px-1">.env</code> and enable payments in Site Settings.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mx-auto mt-8 max-w-md rounded-2xl border border-hair/10 bg-surface2/50 p-6 text-left">
                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">What you get</p>
                    <ul class="mt-4 space-y-3">
                        <li v-for="perk in perks" :key="perk" class="flex items-start gap-3 text-sm text-ink">
                            <CheckIcon class="mt-0.5 h-5 w-5 shrink-0 text-accent2" />
                            {{ perk }}
                        </li>
                    </ul>
                </div>

                <div
                    v-if="paymentMethods.length"
                    class="mx-auto mt-8 max-w-md rounded-2xl border border-hair/10 bg-surface2/40 p-6 text-left"
                >
                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">Choose payment method</p>

                    <div class="mt-4 space-y-2">
                        <button
                            v-for="method in paymentMethods"
                            :key="method.id"
                            type="button"
                            class="flex w-full items-start gap-3 rounded-xl border px-4 py-3 text-left transition"
                            :class="selectedMethod === method.id
                                ? 'border-accent bg-accent/10'
                                : 'border-hair/15 bg-bg/40 hover:border-hair/30'"
                            @click="selectedMethod = method.id"
                        >
                            <component :is="methodIcon(method.id)" class="mt-0.5 h-5 w-5 shrink-0 text-accent" />
                            <span class="min-w-0">
                                <span class="block font-semibold text-ink">{{ method.label }}</span>
                                <span class="mt-0.5 block text-xs text-muted">{{ method.description }}</span>
                                <span
                                    v-if="method.id !== 'prepaid' && !method.configured"
                                    class="mt-1 block text-xs text-amber-700 dark:text-amber-300"
                                >
                                    Temporarily unavailable
                                </span>
                            </span>
                        </button>
                    </div>

                    <div
                        v-if="selectedMethod !== 'prepaid'"
                        class="mt-4 rounded-xl border border-dashed border-hair/20 bg-bg/50 px-4 py-6 text-center"
                    >
                        <p class="text-2xl font-extrabold text-ink">
                            {{ formattedPrice }}<span class="text-base font-medium text-muted">/mo</span>
                        </p>
                        <p class="mt-2 text-xs text-muted">
                            <template v-if="selectedMethod === 'paypal'">Billed monthly through PayPal</template>
                            <template v-else>Card, Apple Pay, Google Pay, and Link via Stripe</template>
                        </p>
                    </div>

                    <div v-else class="mt-4">
                        <label for="access-code" class="block text-sm font-medium text-ink">Access code</label>
                        <input
                            id="access-code"
                            v-model="redeem.code"
                            type="text"
                            autocomplete="off"
                            placeholder="XXXX-XXXX-XXXX"
                            class="mt-2 w-full rounded-xl border border-hair/20 bg-bg px-4 py-3 text-sm uppercase tracking-widest text-ink outline-none ring-accent/30 focus:border-accent focus:ring-2"
                        >
                        <p class="mt-2 text-xs text-muted">Redeem a prepaid or crypto access code for premium streaming.</p>
                    </div>
                </div>

                <div class="mt-10 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <AppButton
                        v-if="page.props.auth?.user"
                        type="button"
                        class="min-w-[220px]"
                        :disabled="checkout.processing || redeem.processing || !canCheckoutSelected"
                        @click="startCheckout"
                    >
                        <PlayCircleIcon class="h-5 w-5" />
                        <template v-if="selectedMethod === 'prepaid'">
                            {{ canCheckoutSelected ? 'Redeem access code' : 'Enter access code' }}
                        </template>
                        <template v-else>
                            {{ canCheckoutSelected ? `Subscribe — ${formattedPrice}/mo` : 'Checkout unavailable' }}
                        </template>
                    </AppButton>

                    <template v-else>
                        <AppButton :href="route('login')" class="min-w-[200px]">Sign in to subscribe</AppButton>
                        <AppButton :href="route('register')" variant="glass" class="min-w-[200px]">Create account</AppButton>
                    </template>
                </div>

                <p class="mt-6 text-xs text-muted">
                    Secure payments via Stripe or PayPal. Prepaid codes grant fixed-term access. Cancel recurring plans anytime.
                </p>

                <Link :href="route('home')" class="mt-8 inline-block text-sm font-medium text-accent hover:underline">
                    Continue browsing for free
                </Link>
            </div>
        </div>
    </div>
</template>
