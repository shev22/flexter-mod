<script setup>
defineProps({
    label: { type: String, required: true },
});
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border border-hair/10 bg-hair/5 px-2.5 py-0.5 text-[11px] font-medium uppercase tracking-wide text-muted"
    >
        {{ label }}
    </span>
</template>
