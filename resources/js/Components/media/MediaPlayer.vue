<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { XMarkIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/solid';
import {
    buildPlaybackUrl,
    createSessionProgressTracker,
    parsePlaybackMessage,
    saveGuestSessionProgress,
} from '../../lib/playback.js';
import { bumpProgressSilent, touchProgressSilent } from '../../lib/useWatchHistory.js';
import { useBilling } from '../../lib/useBilling.js';

const props = defineProps({
    type: { type: String, required: true },
    id: { type: [Number, String], required: true },
    title: { type: String, default: '' },
    poster: { type: String, default: null },
    runtime: { type: Number, default: null },
    season: { type: Number, default: 1 },
    episode: { type: Number, default: 1 },
    initialProgress: { type: Number, default: 0 },
});

const emit = defineEmits(['close', 'progress']);

const page = usePage();
const { canPlay } = useBilling();
const embedSrc = ref(null);
const playerError = ref(null);
const lastSynced = ref({ percent: props.initialProgress, at: 0 });
const latest = ref({
    percent: props.initialProgress,
    season: props.season,
    episode: props.episode,
});

let sessionTracker = null;

const playback = computed(() => page.props.site?.playback ?? {});
const isEnabled = computed(() => playback.value.enabled !== false && playback.value.provider !== 'disabled');
const usePostMessage = computed(
    () => playback.value.progress_mode === 'postmessage' || playback.value.provider === 'vidphantom',
);

function normalizeIncomingProgress(percent) {
    const next = clampPercent(percent);

    // Ignore spurious 100% from provider unload / ended noise when playback was not near the end.
    if (next >= 100 && latest.value.percent < 90) {
        return latest.value.percent > 0 ? latest.value.percent : next;
    }

    return next;
}

function syncProgress(percent, season = null, episode = null, force = false) {
    const next = clampPercent(Math.max(latest.value.percent, normalizeIncomingProgress(percent)));

    if (next <= 0) {
        return;
    }

    const now = Date.now();
    const delta = next - lastSynced.value.percent;
    const elapsed = now - lastSynced.value.at;

    if (!force && delta < 3 && elapsed < 15_000) {
        latest.value = {
            percent: next,
            season: season ?? latest.value.season,
            episode: episode ?? latest.value.episode,
        };
        emit('progress', {
            progress: next,
            season: latest.value.season,
            episode: latest.value.episode,
        });

        return;
    }

    lastSynced.value = { percent: next, at: now };
    latest.value = {
        percent: next,
        season: season ?? latest.value.season,
        episode: episode ?? latest.value.episode,
    };

    emit('progress', {
        progress: next,
        season: latest.value.season,
        episode: latest.value.episode,
    });

    saveGuestSessionProgress({
        type: props.type,
        id: props.id,
        season: latest.value.season,
        episode: latest.value.episode,
        progress: next,
        title: props.title,
    });

    if (!page.props.auth?.user) {
        return;
    }

    bumpProgressSilent(
        props.type,
        Number(props.id),
        next,
        props.type === 'tv' ? latest.value.season : null,
        props.type === 'tv' ? latest.value.episode : null,
        { keepalive: force },
    );
}

function onMessage(event) {
    if (!usePostMessage.value) {
        return;
    }

    try {
        const parsed = parsePlaybackMessage(event, {
            baseUrl: playback.value.base_url,
            type: props.type,
            id: props.id,
            season: props.season,
            episode: props.episode,
        });

        if (!parsed || String(parsed.id) !== String(props.id) || parsed.type !== props.type) {
            return;
        }

        syncProgress(parsed.progress, parsed.season, parsed.episode);
    } catch {
        // Ignore malformed postMessage payloads from the third-party player.
    }
}

function startSessionTracking() {
    sessionTracker = createSessionProgressTracker({
        runtime: props.runtime,
        type: props.type,
        onTick: (percent) => syncProgress(percent, props.season, props.episode),
    });
    sessionTracker.start();
}

function closePlayer() {
    if (sessionTracker) {
        const final = sessionTracker.stop();
        syncProgress(final, latest.value.season, latest.value.episode, true);
        sessionTracker = null;
    } else {
        syncProgress(latest.value.percent, latest.value.season, latest.value.episode, true);
    }

    emit('close');
}

function onKey(e) {
    if (e.key === 'Escape') {
        closePlayer();
    }
}

function openExternal() {
    if (embedSrc.value) {
        window.open(embedSrc.value, '_blank', 'noopener,noreferrer');
    }
}

const FINISHED_THRESHOLD = 95;

function playbackStartProgress(percent) {
    if (percent >= FINISHED_THRESHOLD) {
        return 0;
    }

    return percent > 0 ? percent : 0;
}

function touchProgressOnOpen(percent) {
    if (percent >= FINISHED_THRESHOLD) {
        return 5;
    }

    return Math.max(5, percent || 5);
}

function clampPercent(value) {
    return Math.round(Math.min(100, Math.max(0, Number(value) || 0)));
}

onMounted(() => {
    if (!isEnabled.value) {
        playerError.value = 'Streaming is disabled on this site.';
        return;
    }

    if (!canPlay.value) {
        playerError.value = 'An active subscription is required to stream.';
        return;
    }

    try {
        embedSrc.value = buildPlaybackUrl({
            type: props.type,
            id: props.id,
            season: props.season,
            episode: props.episode,
            playback: playback.value,
            progressPercent: playbackStartProgress(props.initialProgress || 0),
            runtime: props.runtime,
        });
    } catch (error) {
        console.error('Failed to build playback URL', error);
        playerError.value = 'Could not start playback.';
        return;
    }

    if (page.props.auth?.user) {
        touchProgressSilent(
            props.type,
            Number(props.id),
            touchProgressOnOpen(props.initialProgress || 0),
            props.type === 'tv' ? props.season : null,
            props.type === 'tv' ? props.episode : null,
        );
    }

    if (!usePostMessage.value) {
        startSessionTracking();
    }

    window.addEventListener('message', onMessage);
    document.addEventListener('keydown', onKey);
    document.body.style.overflow = 'hidden';
});

onBeforeUnmount(() => {
    if (sessionTracker) {
        const final = sessionTracker.stop();
        syncProgress(final, latest.value.season, latest.value.episode, true);
        sessionTracker = null;
    } else {
        syncProgress(
            latest.value.percent || props.initialProgress || 5,
            latest.value.season,
            latest.value.episode,
            true,
        );
    }

    window.removeEventListener('message', onMessage);
    document.removeEventListener('keydown', onKey);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-[100] bg-black animate-fade-in"
            role="dialog"
            aria-modal="true"
            :aria-label="title ? `Watch ${title}` : 'Player'"
        >
            <iframe
                v-if="embedSrc"
                :src="embedSrc"
                :title="title"
                class="absolute inset-0 z-0 h-full w-full border-0"
                frameborder="0"
                allow="fullscreen *; encrypted-media *; picture-in-picture *; accelerometer; gyroscope; clipboard-write"
                allowfullscreen
            />

            <div class="pointer-events-none absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-4 bg-gradient-to-b from-black/90 via-black/50 to-transparent px-4 py-4 sm:px-6">
                <div class="min-w-0">
                    <p v-if="title" class="truncate font-display text-sm font-bold text-white sm:text-base">{{ title }}</p>
                    <p class="text-xs text-white/60">
                        <template v-if="type === 'tv'">Season {{ season }} · Episode {{ episode }}</template>
                        <template v-else>Press play to start</template>
                    </p>
                </div>
                <div class="pointer-events-auto flex items-center gap-2">
                    <button
                        v-if="embedSrc"
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                        aria-label="Open player in new tab"
                        title="Open in new tab"
                        @click="openExternal"
                    >
                        <ArrowTopRightOnSquareIcon class="h-4 w-4" />
                    </button>
                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                        aria-label="Close player"
                        @click="closePlayer"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div
                v-if="!embedSrc"
                class="absolute inset-0 z-10 flex flex-col items-center justify-center px-6 text-center"
            >
                <div
                    v-if="poster"
                    class="mb-6 aspect-[2/3] w-40 overflow-hidden rounded-2xl ring-1 ring-white/10 shadow-2xl"
                >
                    <img :src="poster" :alt="title" class="h-full w-full object-cover" />
                </div>
                <h2 class="font-display text-xl font-bold text-white">
                    {{ playerError ? 'Playback unavailable' : 'Loading player…' }}
                </h2>
                <p class="mt-2 max-w-sm text-sm leading-relaxed text-white/65">
                    {{ playerError || 'Preparing your stream.' }}
                </p>
            </div>
        </div>
    </Teleport>
</template>
