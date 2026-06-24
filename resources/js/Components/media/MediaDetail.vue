<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    PlayIcon,
    PlusIcon,
    CheckIcon,
    StarIcon,
    ClockIcon,
    EyeIcon,
    MoonIcon,
} from '@heroicons/vue/24/solid';
import { route } from 'ziggy-js';
import { runtime as fmtRuntime, slugify } from '../../lib/format.js';
import { useWatchlist } from '../../lib/useWatchlist.js';
import { useWatchHistory } from '../../lib/useWatchHistory.js';
import { useTonightQueue } from '../../lib/useTonightQueue.js';
import { shouldSpoilerBlur } from '../../lib/spoiler.js';
import { allowBackgroundTrailer, trailerModalEmbedUrl } from '../../lib/trailer.js';
import Rail from '../ui/Rail.vue';
import MediaCard from '../ui/MediaCard.vue';
import RatingRing from '../ui/RatingRing.vue';
import GenrePill from '../ui/GenrePill.vue';
import TrailerBackground from './TrailerBackground.vue';
import TrailerModal from './TrailerModal.vue';
import MediaPlayer from './MediaPlayer.vue';
import CommentSection from '../comments/CommentSection.vue';

const props = defineProps({
    media: { type: Object, required: true },
    watchProgress: { type: Number, default: null },
    watchContext: { type: Object, default: null },
    comments: {
        type: Object,
        default: () => ({ threads: [], total: 0 }),
    },
});

const page = usePage();
const { toggle } = useWatchlist();
const { bump, markWatched } = useWatchHistory();
const { add: addTonight, has: inTonightQueue } = useTonightQueue();
const showPlayer = ref(false);
const showTrailer = ref(false);
const modalEmbedSrc = ref(null);
const heroHover = ref(false);
const showMiniHeader = ref(false);
const progress = ref(props.watchProgress ?? 0);
const selectedSeason = ref(props.watchContext?.season ?? props.media.seasons?.[0]?.season ?? 1);
const selectedEpisode = ref(props.watchContext?.episode ?? 1);
const overviewRevealed = ref(false);

const runtimeLabel = computed(() => fmtRuntime(props.media.runtime));
const allowTrailer = computed(() => allowBackgroundTrailer(page.props.settings));
const showBackgroundTrailer = computed(() => heroHover.value && allowTrailer.value && !!props.media.trailer);
const isWatched = computed(() => progress.value >= 100);
const blurOverview = computed(() => shouldSpoilerBlur(page.props.settings) && !overviewRevealed.value);
const hasSeasons = computed(() => (props.media.seasons?.length ?? 0) > 0);
const episodeOptions = computed(() => {
    const season = props.media.seasons?.find((s) => s.season === selectedSeason.value);
    const count = season?.episodes ?? 1;
    return Array.from({ length: count }, (_, i) => i + 1);
});
const tonightAdded = computed(() => inTonightQueue(props.media.type, props.media.id));

watch(
    () => props.watchContext,
    (ctx) => {
        if (ctx?.season) selectedSeason.value = ctx.season;
        if (ctx?.episode) selectedEpisode.value = ctx.episode;
    },
);

watch(
    () => [props.media.type, props.media.id],
    () => {
        progress.value = props.watchProgress ?? 0;
        selectedSeason.value = props.watchContext?.season ?? props.media.seasons?.[0]?.season ?? 1;
        selectedEpisode.value = props.watchContext?.episode ?? 1;
        overviewRevealed.value = false;
        showPlayer.value = false;
        showTrailer.value = false;
        modalEmbedSrc.value = null;
        heroHover.value = false;
        showMiniHeader.value = false;
    },
);

function castHref(member) {
    return route('actor.show', { slug: slugify(member.name), id: member.id });
}

function historyPayload() {
    if (props.media.type !== 'tv') return {};
    return { season: selectedSeason.value, episode: selectedEpisode.value };
}

function openPlayer() {
    showPlayer.value = true;
}

function playTrailer() {
    if (!props.media.trailer) {
        return;
    }

    modalEmbedSrc.value = trailerModalEmbedUrl(props.media.trailer, page.props.settings);
    showTrailer.value = true;
}

function onPlayerProgress({ progress: percent, season, episode }) {
    if (percent <= 0) {
        return;
    }

    if (season != null) {
        selectedSeason.value = season;
    }

    if (episode != null) {
        selectedEpisode.value = episode;
    }

    progress.value = Math.max(progress.value, Math.min(100, percent));
}

function closePlayer() {
    showPlayer.value = false;
}

function onTrailerProgress(percent) {
    showTrailer.value = false;
    modalEmbedSrc.value = null;
    if (percent <= 0) return;

    const bumpAmount = Math.max(progress.value, Math.min(100, percent));
    progress.value = bumpAmount;
    const extra = historyPayload();
    bump(props.media.type, props.media.id, bumpAmount, extra.season ?? null, extra.episode ?? null);
}

function closeTrailer() {
    showTrailer.value = false;
    modalEmbedSrc.value = null;
}

function markAsWatched() {
    const extra = historyPayload();
    markWatched(
        props.media.type,
        props.media.id,
        () => {
            progress.value = 100;
        },
        extra.season ?? null,
        extra.episode ?? null,
    );
}

function addToTonight() {
    addTonight({
        type: props.media.type,
        id: props.media.id,
        title: props.media.title,
        poster: props.media.poster,
    });
}

function onScroll() {
    showMiniHeader.value = window.scrollY > 320;
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
    <Head :title="media.title" />

    <article>
        <!-- sticky mini header -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0 -translate-y-2"
            leave-active-class="transition duration-150"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="showMiniHeader"
                class="fixed inset-x-0 top-16 z-20 border-b border-hair/10 bg-bg/90 px-4 py-3 backdrop-blur-xl lg:px-12"
            >
                <div class="mx-auto flex max-w-6xl items-center gap-4">
                    <img v-if="media.poster" :src="media.poster" :alt="media.title" class="h-10 w-7 rounded object-cover ring-1 ring-hair/10" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-display text-sm font-bold text-ink">{{ media.title }}</p>
                        <div v-if="watchProgress !== null && progress > 0" class="mt-1 h-1 max-w-xs overflow-hidden rounded-full bg-hair/15">
                            <div class="h-full rounded-full bg-accent transition-all" :style="{ width: `${progress}%` }" />
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn-play inline-flex shrink-0 items-center gap-1 px-3 py-1.5 text-xs"
                        @click="openPlayer"
                    >
                        <PlayIcon class="h-3.5 w-3.5" /> Watch
                    </button>
                    <button type="button" class="rounded-full glass-strong p-2 text-ink" @click="toggle(media)">
                        <CheckIcon v-if="media.in_watchlist" class="h-5 w-5 text-accent2" />
                        <PlusIcon v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </Transition>

        <!-- cinematic header -->
        <header
            class="relative pb-28"
            @mouseenter="heroHover = true"
            @mouseleave="heroHover = false"
        >
            <div class="absolute inset-0 overflow-hidden">
                <img v-if="media.backdrop" :src="media.backdrop" :alt="media.title" class="relative z-0 h-full w-full object-cover" />
                <TrailerBackground
                    v-if="media.trailer"
                    :youtube-key="media.trailer"
                    :title="`${media.title} trailer`"
                    :active="showBackgroundTrailer"
                    vignette
                />
                <!-- Light theme: cinematic dark scrim over backdrop -->
                <div
                    class="pointer-events-none absolute inset-0 z-[3] transition-opacity duration-700 dark:hidden"
                    :class="showBackgroundTrailer ? 'opacity-50' : 'opacity-100'"
                >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-black/20" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/40 to-transparent" />
                    <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-black/90 via-black/50 to-transparent" />
                </div>
                <!-- Dark theme: fade into page background -->
                <div
                    class="pointer-events-none absolute inset-0 z-[3] hidden transition-opacity duration-700 dark:block"
                    :class="showBackgroundTrailer ? 'opacity-40' : 'opacity-100'"
                >
                    <div class="absolute inset-0 bg-gradient-to-t from-bg via-bg/70 to-bg/30" />
                    <div class="absolute inset-0 bg-gradient-to-r from-bg/80 to-transparent" />
                </div>
                <div class="pointer-events-none absolute inset-x-0 bottom-0 z-[5] hidden h-40 bg-gradient-to-t from-bg to-transparent dark:block" />
            </div>

            <div class="relative z-[4] px-4 pb-8 pt-[26vh] lg:px-12">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-end">
                    <div class="hidden w-56 shrink-0 overflow-hidden rounded-2xl shadow-card ring-1 ring-hair/10 sm:block">
                        <img v-if="media.poster" :src="media.poster" :alt="media.title" class="aspect-[2/3] w-full object-cover" />
                    </div>

                    <div class="max-w-3xl">
                        <span class="rounded-full bg-aurora px-3 py-1 text-xs font-bold uppercase tracking-wider text-white shadow-glow">
                            {{ media.type === 'tv' ? 'Series' : 'Movie' }}
                        </span>
                        <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight text-white drop-shadow-lg sm:text-6xl">
                            {{ media.title }}
                        </h1>
                        <p v-if="media.tagline" class="mt-2 text-lg italic text-white/70">{{ media.tagline }}</p>

                        <div class="mt-5 flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2">
                                <RatingRing :value="media.rating" :size="46" />
                                <span class="text-xs text-white/70">{{ media.vote_count?.toLocaleString() }} votes</span>
                            </div>
                            <span v-if="media.year" class="flex items-center gap-1.5 text-sm font-medium text-white">
                                <StarIcon class="h-4 w-4 text-yellow-400" /> {{ media.year }}
                            </span>
                            <span v-if="runtimeLabel" class="flex items-center gap-1.5 text-sm font-medium text-white">
                                <ClockIcon class="h-4 w-4 text-accent" /> {{ runtimeLabel }}
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <GenrePill v-for="g in media.genres" :key="g" :label="g" />
                        </div>

                        <div v-if="hasSeasons" class="mt-5 flex flex-wrap items-end gap-3">
                            <label class="block">
                                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-white/70">Season</span>
                                <select
                                    v-model.number="selectedSeason"
                                    class="rounded-xl border border-white/20 bg-black/40 px-3 py-2 text-sm text-white backdrop-blur"
                                >
                                    <option v-for="s in media.seasons" :key="s.season" :value="s.season">{{ s.name }}</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-white/70">Episode</span>
                                <select
                                    v-model.number="selectedEpisode"
                                    class="rounded-xl border border-white/20 bg-black/40 px-3 py-2 text-sm text-white backdrop-blur"
                                >
                                    <option v-for="ep in episodeOptions" :key="ep" :value="ep">Episode {{ ep }}</option>
                                </select>
                            </label>
                        </div>

                        <p
                            class="mt-5 max-w-2xl text-base leading-relaxed text-white/85 transition"
                            :class="blurOverview ? 'select-none blur-md' : ''"
                        >
                            {{ media.overview }}
                        </p>
                        <button
                            v-if="shouldSpoilerBlur(page.props.settings) && !overviewRevealed"
                            type="button"
                            class="mt-2 text-xs font-semibold text-accent2 underline-offset-2 hover:underline"
                            @click="overviewRevealed = true"
                        >
                            Reveal overview
                        </button>

                        <div v-if="watchProgress !== null && progress > 0" class="mt-5 max-w-md">
                            <div class="mb-1.5 flex items-center justify-between text-xs text-white/80">
                                <span>Your progress</span>
                                <span>{{ progress }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-white/20">
                                <div class="h-full rounded-full bg-accent transition-all" :style="{ width: `${progress}%` }" />
                            </div>
                        </div>

                        <div class="mt-7 flex max-w-full flex-wrap items-center gap-2 overflow-x-auto no-scrollbar sm:flex-nowrap">
                            <button
                                type="button"
                                class="btn-play inline-flex shrink-0 items-center gap-1.5 px-3.5 py-1.5 text-xs"
                                @click="openPlayer"
                            >
                                <PlayIcon class="h-3.5 w-3.5" /> Watch now
                            </button>
                            <button
                                v-if="media.trailer"
                                type="button"
                                class="cinema-btn shrink-0 gap-1.5 px-3.5 py-1.5 text-xs"
                                @click="playTrailer"
                            >
                                <PlayIcon class="h-3.5 w-3.5" /> Trailer
                            </button>
                            <button
                                type="button"
                                class="cinema-btn shrink-0 gap-1.5 px-3.5 py-1.5 text-xs"
                                @click="toggle(media)"
                            >
                                <CheckIcon v-if="media.in_watchlist" class="h-3.5 w-3.5 text-accent2" />
                                <PlusIcon v-else class="h-3.5 w-3.5" />
                                {{ media.in_watchlist ? 'Saved' : 'Watchlist' }}
                            </button>
                            <button
                                type="button"
                                class="cinema-btn shrink-0 gap-1.5 px-3.5 py-1.5 text-xs"
                                :class="tonightAdded ? '!border-accent2/50 !bg-accent2/20' : ''"
                                @click="addToTonight"
                            >
                                <MoonIcon class="h-3.5 w-3.5" />
                                {{ tonightAdded ? 'In tonight\'s queue' : 'Add to tonight\'s queue' }}
                            </button>
                            <button
                                v-if="watchProgress !== null"
                                type="button"
                                class="cinema-btn shrink-0 gap-1.5 px-3.5 py-1.5 text-xs"
                                :class="isWatched ? '!border-emerald-400/40 !bg-emerald-500/25' : ''"
                                @click="markAsWatched"
                            >
                                <EyeIcon class="h-3.5 w-3.5" />
                                {{ isWatched ? 'Watched' : 'Mark watched' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="relative bg-bg">
            <div class="pointer-events-none absolute inset-x-0 top-0 z-0 h-10 bg-gradient-to-b from-black/25 to-bg dark:hidden" />
            <div class="relative z-10 space-y-14 px-4 pb-14 pt-2 lg:px-12">
            <section v-if="media.cast?.length">
                <h2 class="mb-5 font-display text-2xl font-bold text-ink">Top billed cast</h2>
                <div class="no-scrollbar flex gap-4 overflow-x-auto pb-2">
                    <Link
                        v-for="member in media.cast"
                        :key="member.id"
                        :href="castHref(member)"
                        class="group w-28 shrink-0 text-center"
                    >
                        <div class="aspect-square w-full overflow-hidden rounded-full bg-surface2 ring-1 ring-hair/10 transition group-hover:ring-accent/70">
                            <img v-if="member.profile" :src="member.profile" :alt="member.name" class="h-full w-full object-cover" />
                            <div v-else class="grid h-full w-full place-items-center bg-aurora-soft text-xs text-muted">{{ member.name }}</div>
                        </div>
                        <p class="mt-2 line-clamp-1 text-sm font-semibold text-ink group-hover:text-accent">{{ member.name }}</p>
                        <p class="line-clamp-1 text-xs text-muted">{{ member.character }}</p>
                    </Link>
                </div>
            </section>

            <CommentSection :media="media" :comments="comments" />

            <Rail v-if="media.related?.length" :title="media.type === 'tv' ? 'More like this' : 'You may also like'">
                <MediaCard v-for="item in media.related" :key="item.id" :item="item" />
            </Rail>
            </div>
        </div>

        <MediaPlayer
            v-if="showPlayer"
            :type="media.type"
            :id="media.id"
            :title="media.title"
            :poster="media.poster"
            :runtime="media.runtime"
            :season="selectedSeason"
            :episode="selectedEpisode"
            :initial-progress="progress"
            @close="closePlayer"
            @progress="onPlayerProgress"
        />

        <TrailerModal
            v-if="showTrailer"
            :youtube-key="media.trailer"
            :embed-src="modalEmbedSrc"
            :title="media.title"
            :poster="media.poster"
            @close="closeTrailer"
            @progress="onTrailerProgress"
        />
    </article>
</template>
