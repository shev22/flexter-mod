<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    MagnifyingGlassIcon,
    XMarkIcon,
    StarIcon,
    FilmIcon,
    TvIcon,
    UserIcon,
    BookmarkIcon as BookmarkSolid,
    HeartIcon as HeartSolid,
    PlusIcon,
    CheckIcon,
} from '@heroicons/vue/24/solid';
import { HeartIcon as HeartOutline } from '@heroicons/vue/24/outline';
import { slugify, mediaTypeLabel, trimText } from '../../lib/format.js';
import { useWatchlist } from '../../lib/useWatchlist.js';

const emit = defineEmits(['close']);

const query = ref('');
const results = ref([]);
const loading = ref(false);
const input = ref(null);
const IMG = 'https://image.tmdb.org/t/p/w154';

const { toggle, toggleActor } = useWatchlist();

let abortController;
let timer;
watch(query, (value) => {
    clearTimeout(timer);
    abortController?.abort();
    if (!value || value.length < 2) {
        results.value = [];
        loading.value = false;
        return;
    }
    loading.value = true;
    timer = setTimeout(fetchResults, 320);
});

const grouped = computed(() => {
    const movies = results.value.filter((r) => r.type === 'movie');
    const tv = results.value.filter((r) => r.type === 'tv');
    const people = results.value.filter((r) => r.type === 'person');

    return [
        { key: 'movie', label: 'Movies', icon: FilmIcon, items: movies },
        { key: 'tv', label: 'TV Shows', icon: TvIcon, items: tv },
        { key: 'person', label: 'People', icon: UserIcon, items: people },
    ].filter((g) => g.items.length);
});

function normalizeResults(payload) {
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.data)) return payload.data;
    return [];
}

async function fetchResults() {
    abortController?.abort();
    abortController = new AbortController();

    try {
        const { data } = await window.axios.get(route('api.search'), {
            params: { query: query.value },
            signal: abortController.signal,
        });
        results.value = normalizeResults(data.results)
            .filter((r) => r.title)
            .slice(0, 12);
    } catch (e) {
        if (e?.code !== 'ERR_CANCELED') {
            results.value = [];
        }
    } finally {
        loading.value = false;
    }
}

function hrefFor(item) {
    const params = { slug: slugify(item.title), id: item.id };
    if (item.type === 'tv') return route('tv.show', params);
    if (item.type === 'person') return route('actor.show', params);
    return route('movie.show', params);
}

function goTo(item) {
    router.visit(hrefFor(item));
    emit('close');
}

function toggleItem(item, event) {
    event?.stopPropagation();
    event?.preventDefault();

    if (item.type === 'person') {
        toggleActor(item.id, item.in_watchlist, (value) => {
            item.in_watchlist = value;
        });
        return;
    }

    toggle(item);
}

function typeBadgeClass(type) {
    if (type === 'tv') return 'bg-violet-500/15 text-violet-600 dark:text-violet-300';
    if (type === 'person') return 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-300';
    return 'bg-aurora-soft text-accent';
}

function subtitle(item) {
    if (item.type === 'person') return item.known_for || 'Actor';
    return item.release_date || '';
}

function description(item) {
    return trimText(item.overview, 120);
}

function ratingLabel(item) {
    const value = Number(item.rating ?? item.vote_average ?? 0);
    return value > 0 ? value.toFixed(1) : null;
}

function watchlistLabel(item) {
    if (item.type === 'person') {
        return item.in_watchlist ? 'Following' : 'Follow';
    }
    return item.in_watchlist ? 'Saved' : 'Watchlist';
}

function submit() {
    if (!query.value) return;
    router.visit(route('search.show', { query: query.value }));
    emit('close');
}

function onKey(e) {
    if (e.key === 'Escape') emit('close');
}

onMounted(() => {
    document.addEventListener('keydown', onKey);
    nextTick(() => input.value?.focus());
});
onBeforeUnmount(() => document.removeEventListener('keydown', onKey));
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-[90] flex items-start justify-center bg-black/70 p-4 pt-[10vh] backdrop-blur-sm animate-fade-in"
            @click.self="emit('close')"
        >
            <div class="w-full max-w-3xl overflow-hidden rounded-3xl glass-strong shadow-card ring-1 ring-hair/10">
                <form class="flex items-center gap-3 border-b border-hair/10 px-5 py-4" @submit.prevent="submit">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-aurora-soft">
                        <MagnifyingGlassIcon class="h-5 w-5 text-accent" />
                    </div>
                    <input
                        ref="input"
                        v-model="query"
                        type="text"
                        placeholder="Search movies, shows and people…"
                        class="flex-1 bg-transparent text-base text-ink placeholder:text-muted focus:outline-none"
                    />
                    <kbd class="hidden rounded-lg border border-hair/15 bg-surface2 px-2 py-1 text-[10px] font-semibold text-muted sm:inline">ESC</kbd>
                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full text-muted transition hover:bg-hair/10 hover:text-ink"
                        @click="emit('close')"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </form>

                <div class="max-h-[60vh] overflow-y-auto p-3 no-scrollbar">
                    <div v-if="loading" class="space-y-3 p-1">
                        <div v-for="n in 4" :key="n" class="flex gap-3 rounded-2xl p-3">
                            <div class="shimmer h-[88px] w-14 shrink-0 rounded-xl bg-hair/5" />
                            <div class="flex flex-1 flex-col justify-center gap-2">
                                <div class="shimmer h-4 w-2/3 rounded bg-hair/5" />
                                <div class="shimmer h-3 w-1/4 rounded bg-hair/5" />
                                <div class="shimmer h-3 w-full rounded bg-hair/5" />
                                <div class="shimmer h-8 w-24 rounded-full bg-hair/5" />
                            </div>
                        </div>
                    </div>

                    <template v-else-if="results.length">
                        <div v-for="group in grouped" :key="group.key" class="mb-4 last:mb-0">
                            <div class="mb-2 flex items-center gap-2 px-2">
                                <component :is="group.icon" class="h-4 w-4 text-accent" />
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-muted">{{ group.label }}</p>
                                <span class="rounded-full bg-hair/10 px-2 py-0.5 text-[10px] font-semibold text-muted">{{ group.items.length }}</span>
                            </div>

                            <ul class="space-y-2">
                                <li v-for="item in group.items" :key="`${item.type}-${item.id}`">
                                    <div
                                        class="group flex items-stretch gap-3 overflow-hidden rounded-2xl border border-hair/10 bg-surface2/40 p-3 transition hover:border-accent/30 hover:bg-hair/5 hover:shadow-sm"
                                    >
                                        <button
                                            type="button"
                                            class="flex min-w-0 flex-1 items-start gap-3 text-left"
                                            @click="goTo(item)"
                                        >
                                            <div
                                                class="shrink-0 overflow-hidden bg-surface2 ring-1 ring-hair/10"
                                                :class="item.type === 'person' ? 'mt-0.5 h-14 w-14 rounded-full' : 'h-[88px] w-14 rounded-xl'"
                                            >
                                                <img
                                                    v-if="item.poster_path"
                                                    :src="`${IMG}${item.poster_path}`"
                                                    :alt="item.title"
                                                    loading="lazy"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="grid h-full w-full place-items-center bg-aurora-soft text-xs font-bold text-accent"
                                                >
                                                    {{ item.title?.[0] }}
                                                </div>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="line-clamp-1 text-sm font-semibold text-ink group-hover:text-accent">
                                                        {{ item.title }}
                                                    </p>
                                                    <span
                                                        class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                                                        :class="typeBadgeClass(item.type)"
                                                    >
                                                        {{ mediaTypeLabel(item.type) }}
                                                    </span>
                                                </div>

                                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                                    <span v-if="subtitle(item)" class="font-medium text-muted">{{ subtitle(item) }}</span>
                                                    <span
                                                        v-if="item.type !== 'person'"
                                                        class="inline-flex items-center gap-1 rounded-full bg-yellow-400/15 px-2 py-0.5 font-bold text-yellow-600 dark:text-yellow-400"
                                                    >
                                                        <StarIcon class="h-3 w-3" />
                                                        {{ ratingLabel(item) ?? 'NR' }}
                                                    </span>
                                                </div>

                                                <p
                                                    v-if="item.type !== 'person' && description(item)"
                                                    class="mt-2 line-clamp-2 text-xs leading-relaxed text-muted"
                                                >
                                                    {{ description(item) }}
                                                </p>
                                            </div>
                                        </button>

                                        <button
                                            type="button"
                                            class="flex shrink-0 flex-col items-center justify-center gap-1 self-center rounded-xl px-3 py-2 text-xs font-semibold transition"
                                            :class="item.in_watchlist
                                                ? 'bg-aurora text-white shadow-glow'
                                                : 'border border-hair/15 bg-surface2 text-ink hover:border-accent/40 hover:bg-aurora-soft hover:text-accent'"
                                            :aria-label="item.in_watchlist ? 'Remove from watchlist' : 'Add to watchlist'"
                                            @click="toggleItem(item, $event)"
                                        >
                                            <template v-if="item.type === 'person'">
                                                <HeartSolid v-if="item.in_watchlist" class="h-4 w-4" />
                                                <HeartOutline v-else class="h-4 w-4" />
                                            </template>
                                            <template v-else>
                                                <CheckIcon v-if="item.in_watchlist" class="h-4 w-4" />
                                                <PlusIcon v-else class="h-4 w-4" />
                                            </template>
                                            <span>{{ watchlistLabel(item) }}</span>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <button
                            v-if="query.length >= 2"
                            type="button"
                            class="mt-3 w-full rounded-2xl border border-dashed border-hair/20 py-3 text-center text-sm font-semibold text-accent transition hover:border-accent/40 hover:bg-aurora-soft"
                            @click="submit"
                        >
                            See all results for “{{ query }}”
                        </button>
                    </template>

                    <div v-else-if="query.length >= 2" class="p-10 text-center">
                        <p class="text-sm font-medium text-ink">No results for “{{ query }}”</p>
                        <p class="mt-1 text-xs text-muted">Try a different title, actor, or keyword.</p>
                    </div>
                    <div v-else class="p-10 text-center">
                        <p class="text-sm font-medium text-ink">Start typing to explore</p>
                        <p class="mt-1 text-xs text-muted">Movies, TV shows, and people — all in one place.</p>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
