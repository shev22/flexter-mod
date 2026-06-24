<script setup>
import { onBeforeUnmount, onMounted } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';

defineProps({
    open: { type: Boolean, default: false },
});
const emit = defineEmits(['close']);

const shortcuts = [
    { keys: ['g', 'h'], label: 'Go to Home' },
    { keys: ['g', 'm'], label: 'Go to Movies' },
    { keys: ['g', 't'], label: 'Go to TV' },
    { keys: ['g', 'w'], label: 'Go to Watchlist' },
    { keys: ['g', 's'], label: 'Go to Settings' },
    { keys: ['/'], label: 'Open search' },
    { keys: ['?'], label: 'Show this help' },
    { keys: ['Esc'], label: 'Close modals' },
];

function onKey(e) {
    if (e.key === 'Escape') emit('close');
}
onMounted(() => document.addEventListener('keydown', onKey));
onBeforeUnmount(() => document.removeEventListener('keydown', onKey));
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150"
            leave-to-class="opacity-0"
        >
            <div v-if="open" class="fixed inset-0 z-[120] grid place-items-center bg-black/70 p-4 backdrop-blur-sm" @click.self="emit('close')">
                <div class="w-full max-w-md rounded-3xl glass-strong p-6 shadow-card">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-display text-lg font-bold text-ink">Keyboard shortcuts</h2>
                        <button
                            type="button"
                            class="grid h-9 w-9 place-items-center rounded-full text-muted transition hover:bg-hair/10 hover:text-ink"
                            @click="emit('close')"
                        >
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                    </div>

                    <ul class="space-y-3">
                        <li v-for="shortcut in shortcuts" :key="shortcut.label" class="flex items-center justify-between gap-4">
                            <span class="text-sm text-ink">{{ shortcut.label }}</span>
                            <span class="flex shrink-0 gap-1">
                                <kbd
                                    v-for="key in shortcut.keys"
                                    :key="key"
                                    class="rounded-lg border border-hair/15 bg-surface2/80 px-2 py-1 font-mono text-xs font-semibold text-muted"
                                >
                                    {{ key }}
                                </kbd>
                            </span>
                        </li>
                    </ul>

                    <p class="mt-5 text-xs text-muted">Press <kbd class="rounded border border-hair/15 px-1.5 py-0.5 font-mono">?</kbd> anytime to toggle this panel.</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
