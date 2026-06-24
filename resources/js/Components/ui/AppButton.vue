<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    variant: { type: String, default: 'primary' }, // primary | glass | ghost
    href: { type: String, default: null },
    type: { type: String, default: 'button' },
    block: { type: Boolean, default: false },
});

const classes = computed(() => {
    const base =
        'inline-flex items-center justify-center gap-2 rounded-full font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.97] focus:outline-none';
    const size = 'px-5 py-2.5 text-sm';
    const variants = {
        primary: 'bg-aurora text-white shadow-glow hover:brightness-110',
        glass: 'glass-strong text-ink hover:bg-hair/10',
        ghost: 'text-ink hover:bg-hair/10',
    };
    return [base, size, variants[props.variant] ?? variants.primary, props.block ? 'w-full' : ''].join(' ');
});

const tag = computed(() => (props.href ? Link : 'button'));
</script>

<template>
    <component :is="tag" :href="href" :type="href ? undefined : type" :class="classes">
        <slot />
    </component>
</template>
