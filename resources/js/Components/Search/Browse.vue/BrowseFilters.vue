<template>
    <div class="flex space-x-2">
        <button
            v-for="type in mediaTypes"
            :key="type.value"
            @click="$emit('toggle-type', type.value)"
            class="px-4 py-2 rounded-full text-sm font-medium"
            :class="{
        'bg-blue-600 text-white': activeTypes.includes(type.value),
        'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200': !activeTypes.includes(type.value)
      }"
        >
            {{ type.label }}
        </button>
    </div>
</template>

<script setup>
defineProps({
    mediaTypes: {
        type: Array,
        required: true
    },
    activeTypes: {
        type: Array,
        required: true
    }
});

defineEmits(['toggle-type']);
</script>
