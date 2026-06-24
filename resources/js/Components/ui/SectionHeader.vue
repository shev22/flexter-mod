<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
    eyebrow: { type: String, default: null },
    href: { type: String, default: null },
});
</script>

<template>
    <div class="mb-4 flex items-end justify-between gap-4">
        <div>
            <p v-if="eyebrow" class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-accent">
                {{ eyebrow }}
            </p>
            <h2 class="font-display text-xl font-bold text-ink sm:text-2xl">{{ title }}</h2>
        </div>
        <Link
            v-if="href"
            :href="href"
            class="shrink-0 text-sm font-medium text-muted transition hover:text-accent"
        >
            View all →
        </Link>
    </div>
</template>
