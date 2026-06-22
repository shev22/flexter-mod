<script setup>
import { computed, ref, watch } from 'vue';
import { extractYouTubeKey, trailerEmbedUrl } from '../../lib/trailer.js';

const props = defineProps({
    youtubeKey: { type: String, default: null },
    title: { type: String, default: 'Trailer' },
    active: { type: Boolean, default: true },
    /** Soft vignette masks iframe edges (hero + detail pages). */
    vignette: { type: Boolean, default: true },
});

const videoId = computed(() => extractYouTubeKey(props.youtubeKey));
const iframeSrc = ref(null);

// Load the embed only once the player is active (hover). Browsers block autoplay
// on hidden iframes, so setting src on activation keeps playback reliable.
watch(
    () => [props.active, props.youtubeKey],
    ([active, key]) => {
        if (active && extractYouTubeKey(key)) {
            iframeSrc.value = trailerEmbedUrl(key);
            return;
        }

        iframeSrc.value = null;
    },
    { immediate: true },
);
</script>

<template>
    <div
        v-if="videoId"
        class="trailer-bg absolute inset-0 z-[2] overflow-hidden bg-black"
        :class="active && iframeSrc ? 'trailer-bg--active' : 'trailer-bg--idle'"
    >
        <iframe
            v-if="iframeSrc"
            :key="iframeSrc"
            :src="iframeSrc"
            :title="title"
            class="trailer-bg__frame"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            referrerpolicy="strict-origin-when-cross-origin"
        />
        <div
            v-if="vignette"
            class="pointer-events-none absolute inset-0 z-[3]"
            aria-hidden="true"
        >
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_70%_at_50%_45%,transparent_20%,rgba(0,0,0,0.65)_100%)]" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/50" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-transparent to-black/50" />
            <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/60 to-transparent" />
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black via-black/70 to-transparent backdrop-blur-sm" />
        </div>
    </div>
</template>

<style scoped>
.trailer-bg--idle {
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
}

.trailer-bg--active {
    visibility: visible;
    opacity: 1;
}

.trailer-bg__frame {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100vw;
    height: 56.25vw;
    min-width: 177.78vh;
    min-height: 100%;
    border: 0;
    pointer-events: none;
    transform: translate(-50%, -50%) scale(1.18);
}

.trailer-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 4;
    pointer-events: none;
    box-shadow: inset 0 0 100px 50px rgba(0, 0, 0, 0.55);
}
</style>
