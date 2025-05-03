<template>
    <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <div v-else-if="results.length > 0" class="space-y-6 h-screen overflow-y-auto">
        <BrowseResultItem
            v-for="result in results"
            :key="result.id"
            :result="result"
            @navigate="$emit('navigate', result)"
        />
    </div>

    <div v-else class="text-center py-12">
        <p class="text-gray-500 dark:text-gray-400">No content available</p>
    </div>
</template>

<script setup>


import BrowseResultItem from "./BrowseResultItem.vue";

defineProps({
    loading: Boolean,
    results: {
        type: Array,
        required: true
    }
});

defineEmits(['navigate']);
</script>
