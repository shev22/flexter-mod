<template>
    <div class="relative ml-auto">
        <button
            @click="toggleDropdown"
            class="flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
            Sort
        </button>
        <div
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10"
        >
            <div class="py-1">
                <button
                    v-for="option in options"
                    :key="option.value"
                    @click="selectOption(option.value)"
                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="{ 'font-bold': option.value === currentSort }"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    options: {
        type: Array,
        required: true
    },
    currentSort: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['sort']);

const showDropdown = ref(false);

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const selectOption = (option) => {
    emit('sort', option);
    showDropdown.value = false;
};
</script>
