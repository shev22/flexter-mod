<script setup>
import { computed, ref } from 'vue';
import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    options: { type: Array, required: true }, // [{ value, label }]
    placeholder: { type: String, default: 'Select' },
});
const emit = defineEmits(['update:modelValue']);

const open = ref(false);

const selected = computed(() => new Set(props.modelValue.map(String)));

const selectedLabel = computed(() => {
    const count = props.modelValue.length;
    if (!count) return props.placeholder;

    if (count === 1) {
        const value = props.modelValue[0];
        return props.options.find((o) => String(o.value) === String(value))?.label ?? props.placeholder;
    }

    return `${count} selected`;
});

function toggle(value) {
    const key = String(value);
    const next = new Set(selected.value);

    if (next.has(key)) {
        next.delete(key);
    } else {
        next.add(key);
    }

    emit(
        'update:modelValue',
        props.options
            .filter((o) => next.has(String(o.value)))
            .map((o) => o.value),
    );
}

function isSelected(value) {
    return selected.value.has(String(value));
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
            <span :class="modelValue.length ? '' : 'text-muted'">{{ selectedLabel }}</span>
            <ChevronDownIcon class="h-4 w-4 shrink-0 text-muted transition" :class="{ 'rotate-180': open }" />
        </button>

        <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0 -translate-y-1"
            leave-active-class="transition duration-100"
            leave-to-class="opacity-0"
        >
            <div v-if="open" class="absolute z-40 mt-2 w-full min-w-[12rem]">
                <div class="fixed inset-0 -z-10" @click="open = false" />
                <ul class="max-h-64 overflow-auto rounded-xl glass-strong p-1.5 shadow-card no-scrollbar">
                    <li v-for="option in options" :key="String(option.value)">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm transition hover:bg-hair/10"
                            :class="isSelected(option.value) ? 'text-accent' : 'text-ink'"
                            @click="toggle(option.value)"
                        >
                            {{ option.label }}
                            <CheckIcon v-if="isSelected(option.value)" class="h-4 w-4 shrink-0" />
                        </button>
                    </li>
                </ul>
            </div>
        </Transition>
    </div>
</template>
