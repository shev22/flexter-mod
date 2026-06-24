<script setup>
import MediaCard from '../ui/MediaCard.vue';

defineProps({
    items: { type: Array, required: true },
});
</script>

<template>
    <div class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <MediaCard v-for="item in items" :key="`${item.type}-${item.id}`" :item="item" class="animate-fade-up" />
    </div>
</template>
