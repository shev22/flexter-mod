<script setup>
import { computed, ref } from 'vue';
import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    modelValue: { type: [String, Number, null], default: null },
    options: { type: Array, required: true }, // [{ value, label }]
    placeholder: { type: String, default: 'Select' },
});
const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const selectedLabel = computed(
    () => props.options.find((o) => String(o.value) === String(props.modelValue))?.label ?? props.placeholder,
);

function choose(value) {
    emit('update:modelValue', value);
    open.value = false;
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="flex w-full items-center justify-between gap-3 rounded-xl glass px-3.5 py-2.5 text-sm font-medium text-ink transition hover:bg-hair/10"
            :class="{ 'ring-accent': open }"
            @click="open = !open"
        >
            <span :class="modelValue === null ? 'text-muted' : ''">{{ selectedLabel }}</span>
            <ChevronDownIcon class="h-4 w-4 text-muted transition" :class="{ 'rotate-180': open }" />
        </button>

        <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0 -translate-y-1"
            leave-active-class="transition duration-100"
            leave-to-class="opacity-0"
        >
            <div v-if="open" class="absolute z-40 mt-2 w-full">
                <div class="fixed inset-0 -z-10" @click="open = false" />
                <ul class="max-h-64 overflow-auto rounded-xl glass-strong p-1.5 shadow-card no-scrollbar">
                    <li v-for="option in options" :key="String(option.value)">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm transition hover:bg-hair/10"
                            :class="String(option.value) === String(modelValue) ? 'text-accent' : 'text-ink'"
                            @click="choose(option.value)"
                        >
                            {{ option.label }}
                            <CheckIcon v-if="String(option.value) === String(modelValue)" class="h-4 w-4" />
                        </button>
                    </li>
                </ul>
            </div>
        </Transition>
    </div>
</template>
