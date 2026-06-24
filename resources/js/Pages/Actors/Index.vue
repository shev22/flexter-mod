<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { MagnifyingGlassIcon, UserGroupIcon } from '@heroicons/vue/24/solid';
import PersonCard from '../../Components/ui/PersonCard.vue';
import Pagination from '../../Components/ui/Pagination.vue';
import SelectMenu from '../../Components/ui/SelectMenu.vue';
import EmptyState from '../../Components/ui/EmptyState.vue';

const props = defineProps({
    actors: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.search ?? '');
const sort = ref(props.filters.sort ?? 'popularity');

const sortOptions = [
    { value: 'popularity', label: 'Most popular' },
    { value: 'name', label: 'A–Z' },
];

function apply() {
    const params = {};
    if (search.value) params.search = search.value;
    if (sort.value !== 'popularity') params.sort = sort.value;
    router.get(route('actors'), params, { preserveState: true, preserveScroll: true, replace: true });
}

let timer;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(apply, 400);
});
watch(sort, apply);
</script>

<template>
    <Head title="Actors" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">The talent</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">Actors &amp; Creators</h1>
            <p class="mt-3 max-w-2xl text-muted">Meet the people behind your favourite stories and dive into their filmography.</p>
        </div>

        <div class="sticky top-16 z-20 -mx-4 mb-8 flex flex-col gap-3 border-b border-hair/10 bg-bg/80 px-4 py-4 backdrop-blur-xl sm:flex-row sm:items-center lg:px-8">
            <div class="relative flex-1">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" />
                <input v-model="search" type="text" placeholder="Search actors…" class="w-full rounded-xl glass py-2.5 pl-11 pr-4 text-sm text-ink placeholder:text-muted focus:outline-none focus:ring-accent" />
            </div>
            <div class="sm:w-48"><SelectMenu v-model="sort" :options="sortOptions" /></div>
        </div>

        <p class="mb-6 text-sm text-muted"><span class="font-semibold text-ink">{{ actors.total.toLocaleString() }}</span> people</p>

        <div v-if="actors.data.length" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            <PersonCard v-for="person in actors.data" :key="person.id" :person="person" class="animate-fade-up" />
        </div>
        <EmptyState v-else title="No actors found" description="Try a different search term.">
            <template #icon><UserGroupIcon class="h-7 w-7" /></template>
        </EmptyState>

        <Pagination :current-page="actors.current_page" :last-page="actors.last_page" />
    </div>
</template>
