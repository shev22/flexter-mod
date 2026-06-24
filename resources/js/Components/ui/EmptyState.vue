<script setup>
defineProps({
    title: { type: String, default: 'Nothing here yet' },
    description: { type: String, default: null },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center rounded-3xl glass px-6 py-20 text-center">
        <div class="mb-5 grid h-16 w-16 place-items-center rounded-2xl bg-aurora-soft text-accent">
            <slot name="icon" />
        </div>
        <h3 class="font-display text-lg font-semibold text-ink">{{ title }}</h3>
        <p v-if="description" class="mt-1 max-w-sm text-sm text-muted">{{ description }}</p>
        <div class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>
