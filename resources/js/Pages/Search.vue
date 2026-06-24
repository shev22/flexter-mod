<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    MagnifyingGlassIcon,
    StarIcon,
    FilmIcon,
    TvIcon,
    UserIcon,
    BookmarkIcon as BookmarkSolid,
    HeartIcon as HeartSolid,
} from '@heroicons/vue/24/solid';
import { BookmarkIcon as BookmarkOutline, HeartIcon as HeartOutline } from '@heroicons/vue/24/outline';
import { slugify, mediaTypeLabel } from '../lib/format.js';
import { useWatchlist } from '../lib/useWatchlist.js';
import { shouldSpoilerBlur } from '../lib/spoiler.js';
import EmptyState from '../Components/ui/EmptyState.vue';
import AppButton from '../Components/ui/AppButton.vue';
import Pagination from '../Components/ui/Pagination.vue';

const props = defineProps({
    results: { type: Array, default: () => [] },
    query: { type: String, default: '' },
    pagination: {
        type: Object,
        default: () => ({ current_page: 1, last_page: 1, per_page: 24, total: 0 }),
    },
});

const page = usePage();
const blurOverviews = computed(() => shouldSpoilerBlur(page.props.settings));

const IMG = 'https://image.tmdb.org/t/p/w185';
const { toggle, toggleActor } = useWatchlist();

const items = computed(() => props.results.filter((r) => r.title));

const grouped = computed(() => {
    const movies = items.value.filter((r) => r.type === 'movie');
    const tv = items.value.filter((r) => r.type === 'tv');
    const people = items.value.filter((r) => r.type === 'person');

    return [
        { key: 'movie', label: 'Movies', icon: FilmIcon, items: movies },
        { key: 'tv', label: 'TV Shows', icon: TvIcon, items: tv },
        { key: 'person', label: 'People', icon: UserIcon, items: people },
    ].filter((g) => g.items.length);
});

function hrefFor(item) {
    const params = { slug: slugify(item.title), id: item.id };
    if (item.type === 'tv') return route('tv.show', params);
    if (item.type === 'person') return route('actor.show', params);
    return route('movie.show', params);
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
</script>

<template>
    <Head :title="query ? `Search · ${query}` : 'Search'" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Search results</p>
            <h1 class="font-display text-3xl font-extrabold text-ink sm:text-4xl">
                <template v-if="query">Results for “{{ query }}”</template>
                <template v-else>Search</template>
            </h1>
            <p class="mt-2 text-sm text-muted">{{ pagination.total || items.length }} matches</p>
        </div>

        <div v-if="items.length" class="mx-auto max-w-4xl space-y-10">
            <section v-for="group in grouped" :key="group.key">
                <div class="mb-4 flex items-center gap-2">
                    <component :is="group.icon" class="h-5 w-5 text-accent" />
                    <h2 class="font-display text-lg font-bold text-ink">{{ group.label }}</h2>
                    <span class="rounded-full bg-hair/10 px-2.5 py-0.5 text-xs font-semibold text-muted">{{ group.items.length }}</span>
                </div>

                <ul class="space-y-2">
                    <li v-for="item in group.items" :key="`${item.type}-${item.id}`">
                        <div
                            class="group flex items-stretch gap-4 overflow-hidden rounded-2xl border border-hair/10 bg-surface2/30 p-3 transition hover:border-accent/30 hover:bg-hair/5 hover:shadow-sm"
                        >
                            <Link :href="hrefFor(item)" class="flex min-w-0 flex-1 items-center gap-4">
                                <div
                                    class="shrink-0 overflow-hidden bg-surface2 ring-1 ring-hair/10"
                                    :class="item.type === 'person' ? 'h-16 w-16 rounded-full' : 'h-24 w-16 rounded-xl'"
                                >
                                    <img
                                        v-if="item.poster_path"
                                        :src="`${IMG}${item.poster_path}`"
                                        :alt="item.title"
                                        loading="lazy"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    />
                                    <div
                                        v-else
                                        class="grid h-full w-full place-items-center bg-aurora-soft text-sm font-bold text-accent"
                                    >
                                        {{ item.title?.[0] }}
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <p class="line-clamp-1 text-base font-semibold text-ink group-hover:text-accent">
                                            {{ item.title }}
                                        </p>
                                        <span
                                            class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                                            :class="typeBadgeClass(item.type)"
                                        >
                                            {{ mediaTypeLabel(item.type) }}
                                        </span>
                                        <span
                                            v-if="item.type !== 'person' && item.rating > 0"
                                            class="flex shrink-0 items-center gap-0.5 rounded-full bg-yellow-400/15 px-2 py-0.5 text-xs font-bold text-yellow-600 dark:text-yellow-400"
                                        >
                                            <StarIcon class="h-3.5 w-3.5" />
                                            {{ Number(item.rating).toFixed(1) }}
                                        </span>
                                    </div>

                                    <p class="mt-1 text-sm font-medium text-muted">{{ subtitle(item) }}</p>

                                    <p
                                        v-if="item.overview && item.type !== 'person'"
                                        class="mt-2 line-clamp-2 text-sm leading-relaxed text-muted/80 transition"
                                        :class="blurOverviews ? 'select-none blur-sm' : ''"
                                    >
                                        {{ item.overview }}
                                    </p>
                                </div>
                            </Link>

                            <button
                                type="button"
                                class="grid h-11 w-11 shrink-0 place-items-center self-center rounded-xl border border-hair/10 bg-surface2/80 text-muted transition hover:border-accent/40 hover:bg-aurora-soft hover:text-accent"
                                :aria-label="item.in_watchlist ? 'Remove from watchlist' : 'Add to watchlist'"
                                @click="toggleItem(item, $event)"
                            >
                                <template v-if="item.type === 'person'">
                                    <HeartSolid v-if="item.in_watchlist" class="h-5 w-5 text-accent" />
                                    <HeartOutline v-else class="h-5 w-5" />
                                </template>
                                <template v-else>
                                    <BookmarkSolid v-if="item.in_watchlist" class="h-5 w-5 text-accent" />
                                    <BookmarkOutline v-else class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
                    </li>
                </ul>
            </section>

            <Pagination
                v-if="pagination.last_page > 1"
                :current-page="pagination.current_page"
                :last-page="pagination.last_page"
            />
        </div>

        <EmptyState
            v-else
            :title="query ? 'No results' : 'Start searching'"
            :description="query ? 'Try a different title, actor or keyword.' : 'Use the search bar or press / to find movies, shows, and people.'"
        >
            <template #icon><MagnifyingGlassIcon class="h-7 w-7" /></template>
            <template v-if="!query" #action>
                <AppButton :href="route('movies')">Browse movies</AppButton>
            </template>
        </EmptyState>
    </div>
</template>
