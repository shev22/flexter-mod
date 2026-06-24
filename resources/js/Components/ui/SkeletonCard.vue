<script setup>
defineProps({
    aspect: { type: String, default: 'poster' }, // poster | wide
});
</script>

<template>
    <div
        class="shrink-0 animate-pulse overflow-hidden rounded-2xl bg-surface2 ring-1 ring-hair/10"
        :class="aspect === 'wide' ? 'aspect-video w-full' : 'aspect-[2/3] w-36 sm:w-40 lg:w-44'"
    >
        <div class="h-full w-full bg-gradient-to-br from-hair/5 via-surface2 to-hair/10" />
        <div class="mt-2 space-y-2 px-0.5" :class="aspect === 'wide' ? 'hidden' : ''">
            <div class="h-3.5 w-3/4 rounded-md bg-hair/10" />
            <div class="h-3 w-1/2 rounded-md bg-hair/5" />
        </div>
    </div>
</template>
