<template>
    <div class="relative">
        <!-- Search results dropdown -->
        <div
            v-if="show && results.length > 0"
            v-click-outside="onClickOutside"
            class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto"
        >
            <SearchResultItem
                v-for="result in results"
                :key="result.id"
                :result="result"
                @navigate="handleNavigate"
            />
        </div>

        <!-- Loading indicator -->
        <div
            v-if="show && isLoading"
            class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4"
        >
            <div class="flex justify-center py-2">
                <div class="animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-blue-500"></div>
            </div>
        </div>

        <!-- No results message (only shows after search completes with no results) -->
        <div
            v-if="show && searchQuery && results.length === 0 && !isLoading"
            class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4"
        >
            <div class="text-center py-4">
                <p class="text-gray-500 dark:text-gray-400">
                    No results found for "{{ searchQuery }}"
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import SearchResultItem from './SearchResultItem.vue';
import { clickOutside } from '@/directives/clickOutside';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    results: {
        type: Array,
        required: true
    },
    searchQuery: {
        type: String,
        required: true
    },
    isLoading: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['close', 'navigate']);

const onClickOutside = () => {
    emit('close');
};

const handleNavigate = (result) => {
    emit('navigate', result);
};

const vClickOutside = clickOutside;
</script>
