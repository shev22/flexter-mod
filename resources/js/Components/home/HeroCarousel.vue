<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { PlayIcon, PlusIcon, CheckIcon, ChevronLeftIcon, ChevronRightIcon, StarIcon } from '@heroicons/vue/24/solid';
import { detailRoute } from '../../lib/format.js';
import { useWatchlist } from '../../lib/useWatchlist.js';
import { allowBackgroundTrailer } from '../../lib/trailer.js';
import TrailerBackground from '../media/TrailerBackground.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
});

const page = usePage();
const { toggle } = useWatchlist();
const index = ref(0);
const direction = ref(1); // 1 = advancing (slide left), -1 = going back (slide right)
const showTrailer = ref(false);
const hovered = ref(false);
let timer;

const current = computed(() => props.items[index.value] ?? null);

// Movies and tv can share a TMDB id, so a type-qualified key keeps Vue's
// transitions and v-for keys unique.
const currentKey = computed(() => (current.value ? `${current.value.type}-${current.value.id}` : 'none'));

const allowTrailer = computed(() => allowBackgroundTrailer(page.props.settings));
const reduceMotion = computed(() => page.props.settings?.reduce_motion === true);

function syncTrailer() {
    showTrailer.value = !!(hovered.value && allowTrailer.value && !reduceMotion.value && current.value?.trailer);
}

function setIndex(target, dir) {
    const len = props.items.length;
    if (!len) return;
    direction.value = dir;
    index.value = ((target % len) + len) % len;
    restart();
}
function next() {
    setIndex(index.value + 1, 1);
}
function prev() {
    setIndex(index.value - 1, -1);
}
function go(i) {
    if (i === index.value) return;
    setIndex(i, i > index.value ? 1 : -1);
}
function start() {
    if (reduceMotion.value) return;
    clearInterval(timer);
    timer = setInterval(() => go(index.value + 1), 7000);
}
function pause() {
    clearInterval(timer);
}
function restart() {
    if (!document.hidden) {
        start();
    }
}

function onVisibilityChange() {
    if (document.hidden) {
        pause();
    } else if (!hovered.value) {
        start();
    }
}

async function onEnter() {
    hovered.value = true;
    pause();
    await nextTick();
    syncTrailer();
}
function onLeave() {
    hovered.value = false;
    syncTrailer();
    start();
}

watch(index, () => {
    syncTrailer();
});

onMounted(() => {
    start();
    document.addEventListener('visibilitychange', onVisibilityChange);
});
onBeforeUnmount(() => {
    pause();
    document.removeEventListener('visibilitychange', onVisibilityChange);
});
</script>

<template>
    <section
        v-if="current"
        class="relative h-screen min-h-[560px] w-full overflow-hidden"
        @mouseenter="onEnter"
        @mouseleave="onLeave"
    >
        <!-- backdrops: directional slide -->
        <Transition :name="direction === 1 ? 'hero-next' : 'hero-prev'">
            <div :key="currentKey" class="absolute inset-0 z-0">
                <img v-if="current.backdrop" :src="current.backdrop" :alt="current.title" :fetchpriority="index === 0 ? 'high' : undefined" :loading="index === 0 ? undefined : 'lazy'" class="h-full w-full origin-center object-cover animate-kenburns" />
                <div v-else class="h-full w-full bg-aurora" />
            </div>
        </Transition>

        <!-- hover trailer: src is set on activation so autoplay is allowed -->
        <TrailerBackground
            v-if="current.trailer"
            :key="`trailer-${currentKey}`"
            :youtube-key="current.trailer"
            :title="current.title"
            :active="showTrailer"
            class="z-[2]"
        />

        <!-- light theme scrims -->
        <div class="pointer-events-none absolute inset-0 z-[3] transition-opacity duration-700 dark:hidden" :class="showTrailer ? 'opacity-50' : 'opacity-100'">
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/35 to-transparent" />
            <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-black/50 to-transparent" />
            <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-black/70 to-transparent" />
        </div>
        <!-- dark theme scrims -->
        <div class="pointer-events-none absolute inset-0 z-[3] hidden transition-opacity duration-700 dark:block" :class="showTrailer ? 'opacity-40' : 'opacity-100'">
            <div class="absolute inset-0 bg-gradient-to-t from-bg via-bg/40 to-bg/20" />
            <div class="absolute inset-0 bg-gradient-to-r from-bg/90 via-bg/30 to-transparent" />
        </div>

        <!-- content -->
        <div class="relative z-[4] flex h-full items-end pb-16 lg:items-center lg:pb-0">
            <div class="w-full px-4 sm:px-8 lg:pl-24 lg:pr-12 xl:pl-32">
                <div :key="currentKey" class="max-w-2xl animate-fade-up [animation-delay:450ms]">
                        <div class="mb-4 flex items-center gap-3">
                            <span class="rounded-full bg-aurora px-3 py-1 text-xs font-bold uppercase tracking-wider text-white shadow-glow">
                                {{ current.type === 'tv' ? 'Series' : 'Movie' }}
                            </span>
                            <span class="flex items-center gap-1 text-sm font-semibold text-yellow-400">
                                <StarIcon class="h-4 w-4" /> {{ Number(current.rating).toFixed(1) }}
                            </span>
                            <span class="text-sm text-white/70">{{ current.year }}</span>
                        </div>

                        <img
                            v-if="current.logo"
                            :src="current.logo"
                            :alt="current.title"
                            class="max-h-28 w-auto max-w-[80%] object-contain object-left drop-shadow-2xl sm:max-h-36 lg:max-h-44"
                        />
                        <h1
                            v-else
                            class="font-display text-4xl font-extrabold leading-[1.05] text-white drop-shadow-xl sm:text-6xl lg:text-7xl"
                        >
                            {{ current.title }}
                        </h1>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span v-for="g in (current.genres || []).slice(0, 3)" :key="g" class="rounded-full border border-white/20 bg-white/5 px-3 py-1 text-xs font-medium text-white/90 backdrop-blur">
                                {{ g }}
                            </span>
                        </div>

                        <p class="mt-5 line-clamp-3 max-w-xl text-base text-white/80">{{ current.overview }}</p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Link :href="detailRoute(current)" class="btn-play gap-2 px-7 py-3.5">
                                <PlayIcon class="h-5 w-5" /> Watch now
                            </Link>
                            <button type="button" class="cinema-btn gap-2 px-7 py-3.5" @click="toggle(current)">
                                <CheckIcon v-if="current.in_watchlist" class="h-5 w-5 text-accent2" />
                                <PlusIcon v-else class="h-5 w-5" />
                                {{ current.in_watchlist ? 'In Watchlist' : 'Watchlist' }}
                            </button>
                        </div>
                </div>
            </div>
        </div>

        <!-- controls -->
        <div class="absolute bottom-6 right-4 z-10 hidden items-center gap-2 lg:flex lg:right-12">
            <button type="button" class="cinema-btn cinema-btn-icon h-10 w-10 shrink-0" @click="prev">
                <ChevronLeftIcon class="h-5 w-5" />
            </button>
            <button type="button" class="cinema-btn cinema-btn-icon h-10 w-10 shrink-0" @click="next">
                <ChevronRightIcon class="h-5 w-5" />
            </button>
        </div>

        <!-- indicators -->
        <div class="absolute bottom-7 left-4 z-10 flex gap-1.5 lg:left-12">
            <button
                v-for="(item, i) in items"
                :key="`${item.type}-${item.id}`"
                type="button"
                class="h-1.5 rounded-full transition-all duration-300"
                :class="i === index ? 'w-8 bg-accent' : 'w-3 bg-white/30 hover:bg-white/60'"
                @click="go(i)"
            />
        </div>
    </section>
</template>

<style>
.hero-next-enter-active,
.hero-next-leave-active,
.hero-prev-enter-active,
.hero-prev-leave-active {
    transition: transform 450ms cubic-bezier(0.65, 0, 0.35, 1);
}
.hero-next-enter-from {
    transform: translateX(100%);
}
.hero-next-leave-to {
    transform: translateX(-100%);
}
.hero-prev-enter-from {
    transform: translateX(-100%);
}
.hero-prev-leave-to {
    transform: translateX(100%);
}
</style>
