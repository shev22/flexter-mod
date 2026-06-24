<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: { type: [Number, String], default: 0 },
    size: { type: Number, default: 44 },
    stroke: { type: Number, default: 3 },
});

const score = computed(() => Math.max(0, Math.min(10, Number(props.value) || 0)));
const radius = computed(() => (props.size - props.stroke) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);
const offset = computed(() => circumference.value * (1 - score.value / 10));

const tone = computed(() => {
    if (score.value >= 7.5) return 'rgb(34 197 94)';
    if (score.value >= 5) return 'rgb(234 179 8)';
    if (score.value > 0) return 'rgb(244 63 94)';
    return 'rgb(120 120 140)';
});
</script>

<template>
    <div class="relative grid place-items-center" :style="{ width: `${size}px`, height: `${size}px` }">
        <svg :width="size" :height="size" class="-rotate-90">
            <circle
                :cx="size / 2"
                :cy="size / 2"
                :r="radius"
                fill="rgb(8 8 14 / 0.7)"
                :stroke="'rgb(255 255 255 / 0.12)'"
                :stroke-width="stroke"
            />
            <circle
                :cx="size / 2"
                :cy="size / 2"
                :r="radius"
                fill="none"
                :stroke="tone"
                :stroke-width="stroke"
                stroke-linecap="round"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="offset"
                class="transition-[stroke-dashoffset] duration-700"
            />
        </svg>
        <span class="absolute font-display font-semibold text-white" :style="{ fontSize: `${size * 0.3}px` }">
            {{ score > 0 ? score.toFixed(1) : 'NR' }}
        </span>
    </div>
</template>
