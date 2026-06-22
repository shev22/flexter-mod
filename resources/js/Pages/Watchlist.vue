<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { BookmarkIcon, MoonIcon, TrashIcon } from '@heroicons/vue/24/solid';
import MediaCard from '../Components/ui/MediaCard.vue';
import PersonCard from '../Components/ui/PersonCard.vue';
import AppButton from '../Components/ui/AppButton.vue';
import EmptyState from '../Components/ui/EmptyState.vue';
import SelectMenu from '../Components/ui/SelectMenu.vue';
import Pagination from '../Components/ui/Pagination.vue';
import Rail from '../Components/ui/Rail.vue';
import { route } from 'ziggy-js';
import { detailRoute } from '../lib/format.js';
import { useTonightQueue } from '../lib/useTonightQueue.js';

const props = defineProps({
    movies: { type: Object, default: () => ({ data: [], total: 0, current_page: 1, last_page: 1 }) },
    shows: { type: Object, default: () => ({ data: [], total: 0, current_page: 1, last_page: 1 }) },
    actors: { type: Object, default: () => ({ data: [], total: 0, current_page: 1, last_page: 1 }) },
    suggestions: { type: Array, default: () => [] },
});

const { queue, remove: removeTonight, clear: clearTonight, sync } = useTonightQueue();
sync();

const sortOptions = [
    { value: 'added', label: 'Recently added' },
    { value: 'rating', label: 'Highest rated' },
    { value: 'title', label: 'Title A–Z' },
    { value: 'year', label: 'Release year' },
];

const sort = ref(new URLSearchParams(window.location.search).get('sort') || 'added');
const selectMode = ref(false);
const selected = ref([]);

const movieItems = computed(() => props.movies.data ?? []);
const showItems = computed(() => props.shows.data ?? []);
const actorItems = computed(() => props.actors.data ?? []);

const tabs = computed(() => [
    { key: 'movies', label: 'Movies', count: props.movies.total ?? 0 },
    { key: 'shows', label: 'Series', count: props.shows.total ?? 0 },
    { key: 'actors', label: 'Actors', count: props.actors.total ?? 0 },
]);

const tab = ref('movies');
const total = computed(() => (props.movies.total ?? 0) + (props.shows.total ?? 0) + (props.actors.total ?? 0));
const isEmpty = computed(() => total.value === 0);

const activePagination = computed(() => {
    if (tab.value === 'shows') return props.shows;
    if (tab.value === 'actors') return props.actors;
    return props.movies;
});

function applySort(value) {
    sort.value = value;
    router.get(route('watchlist'), { sort: value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['movies', 'shows', 'actors'],
    });
}

function itemKey(item) {
    return `${item.kind}:${item.id}`;
}

function isSelected(item) {
    return selected.value.some((s) => s.type === item.kind && s.id === item.id);
}

function toggleSelect(item) {
    const key = itemKey(item);
    if (isSelected(item)) {
        selected.value = selected.value.filter((s) => `${s.type}:${s.id}` !== key);
    } else {
        selected.value = [...selected.value, { type: item.kind, id: item.id }];
    }
}

function bulkRemove() {
    if (!selected.value.length) return;
    router.delete(route('watchlist.bulk'), {
        data: { items: selected.value },
        preserveScroll: true,
        onSuccess: () => {
            selected.value = [];
            selectMode.value = false;
        },
    });
}
</script>

<template>
    <Head title="My Watchlist" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Saved for later</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">My Watchlist</h1>
            <p class="mt-3 text-muted">{{ total }} items saved across movies, series and people you follow.</p>
        </div>

        <div class="mb-8 flex flex-wrap items-center gap-4">
            <div class="inline-flex rounded-full glass p-1">
                <button
                    v-for="t in tabs"
                    :key="t.key"
                    type="button"
                    class="flex items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold transition"
                    :class="tab === t.key ? 'bg-aurora text-white shadow-glow' : 'text-muted hover:text-ink'"
                    @click="tab = t.key"
                >
                    {{ t.label }}
                    <span class="rounded-full px-1.5 text-xs" :class="tab === t.key ? 'bg-white/20' : 'bg-hair/10'">{{ t.count }}</span>
                </button>
            </div>

            <div class="ml-auto flex flex-wrap items-center gap-3">
                <div class="w-44">
                    <SelectMenu :model-value="sort" :options="sortOptions" @update:model-value="applySort" />
                </div>
                <button
                    v-if="tab !== 'actors'"
                    type="button"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="selectMode ? 'bg-aurora text-white shadow-glow' : 'glass text-ink hover:bg-hair/10'"
                    @click="selectMode = !selectMode; selected = []"
                >
                    {{ selectMode ? 'Cancel' : 'Select' }}
                </button>
                <button
                    v-if="selectMode && selected.length"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full bg-rose-500/15 px-4 py-2 text-sm font-semibold text-rose-400 transition hover:bg-rose-500/25"
                    @click="bulkRemove"
                >
                    <TrashIcon class="h-4 w-4" /> Remove ({{ selected.length }})
                </button>
            </div>
        </div>

        <section v-if="queue.length" class="mb-10 rounded-3xl glass p-5 sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-aurora-soft text-accent">
                        <MoonIcon class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="font-display text-lg font-bold text-ink">Tonight's queue</h2>
                        <p class="text-xs text-muted">{{ queue.length }} picked for tonight</p>
                    </div>
                </div>
                <button type="button" class="text-xs font-semibold text-muted transition hover:text-rose-400" @click="clearTonight">
                    Clear all
                </button>
            </div>
            <div class="no-scrollbar flex gap-3 overflow-x-auto pb-1">
                <div
                    v-for="item in queue"
                    :key="`${item.type}-${item.id}`"
                    class="relative w-28 shrink-0"
                >
                    <Link :href="detailRoute(item)" class="block">
                        <div class="aspect-[2/3] overflow-hidden rounded-xl ring-1 ring-hair/10">
                            <img v-if="item.poster" :src="item.poster" :alt="item.title" loading="lazy" class="h-full w-full object-cover" />
                            <div v-else class="grid h-full w-full place-items-center bg-aurora-soft p-2 text-center text-[10px] text-muted">{{ item.title }}</div>
                        </div>
                        <p class="mt-1 line-clamp-2 text-xs font-semibold text-ink">{{ item.title }}</p>
                    </Link>
                    <button
                        type="button"
                        class="absolute right-1 top-1 grid h-6 w-6 place-items-center rounded-full bg-black/60 text-white"
                        @click="removeTonight(item.type, item.id)"
                    >
                        <TrashIcon class="h-3 w-3" />
                    </button>
                </div>
            </div>
        </section>

        <section v-if="isEmpty && suggestions.length" class="mb-10">
            <Rail title="Suggestions for you" eyebrow="Your watchlist is empty">
                <MediaCard v-for="item in suggestions" :key="`${item.type}-${item.id}`" :item="item" />
            </Rail>
        </section>

        <div v-if="tab === 'movies'">
            <div v-if="movieItems.length" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                <div v-for="item in movieItems" :key="`${item.type}-${item.id}`" class="relative">
                    <label v-if="selectMode" class="absolute left-2 top-2 z-20 flex cursor-pointer items-center gap-2 rounded-full bg-black/60 px-2 py-1">
                        <input type="checkbox" class="rounded border-white/30" :checked="isSelected({ ...item, kind: 'movie' })" @change="toggleSelect({ ...item, kind: 'movie' })" />
                    </label>
                    <MediaCard :item="item" />
                </div>
            </div>
            <EmptyState v-else title="No movies saved yet" description="Browse the catalogue and tap the bookmark to save films here.">
                <template #icon><BookmarkIcon class="h-7 w-7" /></template>
                <template #action><AppButton :href="route('movies')">Browse movies</AppButton></template>
            </EmptyState>
        </div>

        <div v-else-if="tab === 'shows'">
            <div v-if="showItems.length" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                <div v-for="item in showItems" :key="`${item.type}-${item.id}`" class="relative">
                    <label v-if="selectMode" class="absolute left-2 top-2 z-20 flex cursor-pointer items-center gap-2 rounded-full bg-black/60 px-2 py-1">
                        <input type="checkbox" class="rounded border-white/30" :checked="isSelected({ ...item, kind: 'tv' })" @change="toggleSelect({ ...item, kind: 'tv' })" />
                    </label>
                    <MediaCard :item="item" />
                </div>
            </div>
            <EmptyState v-else title="No series saved yet" description="Find your next binge and add it to your watchlist.">
                <template #icon><BookmarkIcon class="h-7 w-7" /></template>
                <template #action><AppButton :href="route('tv')">Browse series</AppButton></template>
            </EmptyState>
        </div>

        <div v-else>
            <div v-if="actorItems.length" class="grid grid-cols-3 gap-x-4 gap-y-8 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8">
                <PersonCard v-for="person in actorItems" :key="person.id" :person="person" />
            </div>
            <EmptyState v-else title="No favourite actors yet" description="Follow actors to keep their work close by.">
                <template #icon><BookmarkIcon class="h-7 w-7" /></template>
                <template #action><AppButton :href="route('actors')">Discover actors</AppButton></template>
            </EmptyState>
        </div>

        <Pagination
            v-if="activePagination.last_page > 1"
            :current-page="activePagination.current_page"
            :last-page="activePagination.last_page"
        />
    </div>
</template>
