<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { LockClosedIcon } from '@heroicons/vue/24/solid';
import AppButton from '../ui/AppButton.vue';
import { useBilling } from '../../lib/useBilling.js';

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const { playbackBlockedReason, formattedPrice } = useBilling();
const message = computed(() => playbackBlockedReason());
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-black/70 px-4 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-label="Subscription required"
                @click.self="emit('close')"
            >
                <div class="w-full max-w-md rounded-3xl glass-strong p-8 text-center shadow-2xl">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-aurora text-white shadow-glow">
                        <LockClosedIcon class="h-7 w-7" />
                    </div>

                    <h2 class="mt-5 font-display text-2xl font-bold text-ink">Streaming is premium</h2>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        {{ message }}
                    </p>
                    <p class="mt-3 text-sm font-semibold text-ink">
                        Flexter Premium — {{ formattedPrice }}/month
                    </p>

                    <div class="mt-8 flex flex-col gap-3">
                        <AppButton :href="route('billing.subscribe')" class="w-full">
                            View plans & subscribe
                        </AppButton>
                        <AppButton variant="ghost" class="w-full" @click="emit('close')">
                            Not now
                        </AppButton>
                    </div>

                    <p class="mt-6 text-xs text-muted">
                        You can still browse, save watchlists, comment, and explore — only playback requires a subscription.
                    </p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
