<script setup>
import { computed } from 'vue';
import { DEFAULT_LIST_ICON, listIconEmoji } from '../../lib/listIcons.js';

const props = defineProps({
    icon: { type: String, default: DEFAULT_LIST_ICON },
    size: { type: String, default: 'md' },
});

const emoji = computed(() => listIconEmoji(props.icon));

const sizeClass = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-9 w-9 text-lg';
        case 'lg':
            return 'h-14 w-14 text-3xl';
        case 'xl':
            return 'h-16 w-16 text-4xl';
        default:
            return 'h-12 w-12 text-2xl';
    }
});
</script>

<template>
    <span
        class="grid shrink-0 place-items-center rounded-2xl bg-aurora-soft ring-1 ring-hair/10"
        :class="sizeClass"
        role="img"
        :aria-label="`${icon} list icon`"
    >
        {{ emoji }}
    </span>
</template>
