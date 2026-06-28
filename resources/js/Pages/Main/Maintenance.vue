<script setup>
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: false });

defineProps({
    siteName: { type: String, default: 'Flexter' },
});
</script>

<template>
    <Head title="Maintenance" />

    <div class="relative flex min-h-screen items-center justify-center bg-bg px-6 text-ink">
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute -left-40 -top-40 h-96 w-96 rounded-full bg-accent/10 blur-3xl" />
            <div class="absolute -right-40 top-1/3 h-96 w-96 rounded-full bg-accent2/10 blur-3xl" />
        </div>

        <div class="w-full max-w-lg rounded-3xl glass-strong p-10 text-center shadow-card">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-400">Maintenance</p>
            <h1 class="mt-4 font-display text-3xl font-extrabold text-ink">{{ siteName }} is down for maintenance</h1>
            <p class="mt-4 text-sm leading-relaxed text-muted">
                We are making improvements and will be back shortly. Thanks for your patience.
            </p>
        </div>
    </div>
</template>
