<script setup>
import { computed, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { ratingOptions, toList } from '../../lib/filters.js';
import MultiSelectMenu from '../ui/MultiSelectMenu.vue';
import SelectMenu from '../ui/SelectMenu.vue';

const props = defineProps({
    routeName: { type: String, required: true },
    filters: { type: Object, required: true },
});

const page = usePage();

const search = ref(props.filters.search ?? '');
const genres = ref(toList(props.filters.genre));
const sort = ref(props.filters.sort ?? 'popularity');
const years = ref(toList(props.filters.year));
const ratings = ref(toList(props.filters.rating));

const genreOptions = computed(() =>
    (page.props.genres ?? []).map((g) => ({ value: g.id, label: g.name })),
);

const sortOptions = [
    { value: 'popularity', label: 'Most popular' },
    { value: 'rating', label: 'Top rated' },
    { value: 'newest', label: 'Newest' },
    { value: 'oldest', label: 'Oldest' },
    { value: 'title', label: 'A–Z' },
];

const now = new Date().getFullYear();
const yearOptions = Array.from({ length: now - 1970 + 1 }, (_, i) => {
    const y = now - i;
    return { value: y, label: String(y) };
});

const hasFilters = computed(
    () =>
        search.value ||
        genres.value.length ||
        years.value.length ||
        ratings.value.length ||
        sort.value !== 'popularity',
);

function apply() {
    const params = {};
    if (search.value) params.search = search.value;
    if (genres.value.length) params.genre = genres.value;
    if (years.value.length) params.year = years.value;
    if (ratings.value.length) params.rating = ratings.value;
    if (sort.value && sort.value !== 'popularity') params.sort = sort.value;

    router.get(route(props.routeName), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['movies', 'shows', 'filters'],
    });
}

function reset() {
    search.value = '';
    genres.value = [];
    years.value = [];
    ratings.value = [];
    sort.value = 'popularity';
    apply();
}

let timer;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(apply, 400);
});
watch([genres, sort, years, ratings], () => {
    clearTimeout(timer);
    timer = setTimeout(apply, 400);
}, { deep: true });
</script>

<template>
    <div class="sticky top-16 z-20 -mx-4 mb-8 border-b border-hair/10 bg-bg/80 px-4 py-4 backdrop-blur-xl sm:top-0 lg:px-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search the library…"
                    class="w-full rounded-xl glass py-2.5 pl-11 pr-4 text-sm text-ink placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-accent"
                />
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:flex lg:flex-wrap">
                <div class="lg:w-40">
                    <MultiSelectMenu v-model="genres" :options="genreOptions" placeholder="Genres" />
                </div>
                <div class="lg:w-36">
                    <MultiSelectMenu v-model="years" :options="yearOptions" placeholder="Years" />
                </div>
                <div class="lg:w-36">
                    <MultiSelectMenu v-model="ratings" :options="ratingOptions" placeholder="Rating" />
                </div>
                <div class="lg:w-44">
                    <SelectMenu v-model="sort" :options="sortOptions" />
                </div>
                <button
                    v-if="hasFilters"
                    type="button"
                    class="col-span-2 inline-flex items-center justify-center gap-1.5 rounded-xl glass px-3 py-2.5 text-sm font-medium text-muted transition hover:text-accent sm:col-span-3 lg:col-span-1"
                    @click="reset"
                >
                    <XMarkIcon class="h-4 w-4" /> Clear
                </button>
            </div>
        </div>
    </div>
</template>
