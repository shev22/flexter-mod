<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { CheckIcon, PlayCircleIcon, SparklesIcon } from '@heroicons/vue/24/solid';
import AppButton from '../../Components/ui/AppButton.vue';
import { useBilling } from '../../lib/useBilling.js';

defineProps({
    billing: { type: Object, required: true },
    stripeKey: { type: String, default: null },
});

const page = usePage();
const { plan, formattedPrice } = useBilling();

const checkout = useForm({});

const perks = [
    'Unlimited movie & series streaming',
    'Continue watching across devices',
    'Full watch history sync',
    'Cancel anytime from your account',
];

function startCheckout() {
    checkout.post(route('billing.checkout'));
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

                <div class="mx-auto mt-8 max-w-md rounded-2xl border border-hair/10 bg-surface2/50 p-6 text-left">
                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">What you get</p>
                    <ul class="mt-4 space-y-3">
                        <li v-for="perk in perks" :key="perk" class="flex items-start gap-3 text-sm text-ink">
                            <CheckIcon class="mt-0.5 h-5 w-5 shrink-0 text-accent2" />
                            {{ perk }}
                        </li>
                    </ul>
                </div>

                <div class="mt-10 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <AppButton
                        v-if="page.props.auth?.user"
                        type="button"
                        class="min-w-[220px]"
                        :disabled="checkout.processing"
                        @click="startCheckout"
                    >
                        <PlayCircleIcon class="h-5 w-5" />
                        Subscribe — {{ formattedPrice }}/mo
                    </AppButton>

                    <template v-else>
                        <AppButton :href="route('login')" class="min-w-[200px]">Sign in to subscribe</AppButton>
                        <AppButton :href="route('register')" variant="glass" class="min-w-[200px]">Create account</AppButton>
                    </template>
                </div>

                <p class="mt-6 text-xs text-muted">
                    Secure checkout powered by Stripe. Billed monthly. You can cancel anytime.
                </p>

                <Link :href="route('home')" class="mt-8 inline-block text-sm font-medium text-accent hover:underline">
                    Continue browsing for free
                </Link>
            </div>
        </div>
    </div>
</template>
