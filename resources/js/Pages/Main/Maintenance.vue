<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    siteName: { type: String, default: 'Flexter' },
});
</script>

<template>
    <Head title="Maintenance" />

    <div class="flex min-h-screen items-center justify-center bg-bg px-4">
        <div class="max-w-md rounded-3xl glass-strong p-10 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-400">Maintenance</p>
            <h1 class="mt-4 font-display text-3xl font-extrabold text-ink">{{ siteName }} is down for maintenance</h1>
            <p class="mt-4 text-sm text-muted">
                We are making improvements and will be back shortly. Thanks for your patience.
            </p>
        </div>
    </div>
</template>
