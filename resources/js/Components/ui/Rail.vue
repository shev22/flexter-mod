<script setup>
import { ref } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid';
import SectionHeader from './SectionHeader.vue';

defineProps({
    title: { type: String, default: null },
    eyebrow: { type: String, default: null },
    href: { type: String, default: null },
});

const track = ref(null);

function scroll(direction) {
    if (!track.value) return;
    const amount = track.value.clientWidth * 0.85;
    track.value.scrollBy({ left: direction * amount, behavior: 'smooth' });
}
</script>

<template>
    <section>
        <div class="flex items-end justify-between gap-4">
            <SectionHeader v-if="title" :title="title" :eyebrow="eyebrow" :href="href" class="flex-1" />
            <div class="mb-4 hidden gap-2 sm:flex">
                <button
                    type="button"
                    class="grid h-9 w-9 place-items-center rounded-full glass text-ink transition hover:bg-hair/10 hover:text-accent"
                    @click="scroll(-1)"
                >
                    <ChevronLeftIcon class="h-5 w-5" />
                </button>
                <button
                    type="button"
                    class="grid h-9 w-9 place-items-center rounded-full glass text-ink transition hover:bg-hair/10 hover:text-accent"
                    @click="scroll(1)"
                >
                    <ChevronRightIcon class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div
            ref="track"
            class="no-scrollbar -mx-1 flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-px-1 px-1 pb-2 [&>*]:w-36 [&>*]:snap-start sm:[&>*]:w-40 lg:[&>*]:w-44"
        >
            <slot />
        </div>
    </section>
</template>
