<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { XMarkIcon, PlayIcon } from '@heroicons/vue/24/solid';
import { extractYouTubeKey, trailerModalEmbedUrl } from '../../lib/trailer.js';

const props = defineProps({
    youtubeKey: { type: String, default: null },
    title: { type: String, default: '' },
    poster: { type: String, default: null },
    /** Optional pre-built src (set synchronously on click for reliable autoplay). */
    embedSrc: { type: String, default: null },
});
const emit = defineEmits(['close', 'progress']);

const page = usePage();
const openedAt = ref(Date.now());
const videoId = computed(() => extractYouTubeKey(props.youtubeKey));
const src = computed(
    () => props.embedSrc || trailerModalEmbedUrl(props.youtubeKey, page.props.settings),
);
const hasVideo = computed(() => !!(src.value && videoId.value));

function closeWithProgress() {
    const elapsed = (Date.now() - openedAt.value) / 1000;
    const percent = hasVideo.value ? Math.min(100, Math.round((elapsed / 10) * 100)) : 0;
    emit('progress', percent);
    emit('close');
}

function onKey(e) {
    if (e.key === 'Escape') closeWithProgress();
}

onMounted(() => {
    openedAt.value = Date.now();
    document.addEventListener('keydown', onKey);
    document.body.style.overflow = 'hidden';
});
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onKey);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-[100] flex flex-col bg-black animate-fade-in"
            role="dialog"
            aria-modal="true"
            :aria-label="title ? `Watch ${title}` : 'Player'"
        >
            <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-4 bg-gradient-to-b from-black/80 to-transparent px-4 py-4 sm:px-6">
                <div class="min-w-0">
                    <p v-if="title" class="truncate font-display text-sm font-bold text-white sm:text-base">{{ title }}</p>
                    <p class="text-xs text-white/60">{{ hasVideo ? 'Trailer' : 'Playback preview' }}</p>
                </div>
                <button
                    type="button"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                    aria-label="Close player"
                    @click="closeWithProgress"
                >
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>

            <div class="relative flex min-h-0 flex-1 items-center justify-center">
                <iframe
                    v-if="hasVideo"
                    :key="videoId"
                    :src="src"
                    :title="title"
                    class="absolute inset-0 h-full w-full"
                    frameborder="0"
                    allow="autoplay; fullscreen; encrypted-media; picture-in-picture"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                />

                <div v-else class="flex max-w-md flex-col items-center px-6 text-center">
                    <div
                        v-if="poster"
                        class="mb-6 aspect-[2/3] w-40 overflow-hidden rounded-2xl ring-1 ring-white/10 shadow-2xl"
                    >
                        <img :src="poster" :alt="title" class="h-full w-full object-cover" />
                    </div>
                    <span class="mb-4 grid h-14 w-14 place-items-center rounded-full bg-white/10 text-white">
                        <PlayIcon class="h-7 w-7" />
                    </span>
                    <h2 class="font-display text-xl font-bold text-white">Full playback coming soon</h2>
                    <p class="mt-2 text-sm leading-relaxed text-white/65">
                        Streaming playback for this title is not available yet. When it launches, Watch now will start the show here.
                    </p>
                    <p v-if="!youtubeKey" class="mt-4 text-xs text-white/45">No trailer is available for this title either.</p>
                </div>
            </div>
        </div>
    </Teleport>
</template>
