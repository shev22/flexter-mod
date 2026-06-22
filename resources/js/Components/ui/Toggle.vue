<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <button
        type="button"
        role="switch"
        :aria-checked="modelValue"
        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-300"
        :class="modelValue ? 'bg-aurora' : 'bg-hair/15'"
        @click="emit('update:modelValue', !modelValue)"
    >
        <span
            class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-300"
            :class="modelValue ? 'translate-x-5' : 'translate-x-0.5'"
        />
    </button>
</template>
