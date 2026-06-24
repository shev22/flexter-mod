<script setup>
import { FilmIcon } from '@heroicons/vue/24/solid';
import FilterBar from './FilterBar.vue';
import MediaGrid from './MediaGrid.vue';
import Pagination from '../ui/Pagination.vue';
import EmptyState from '../ui/EmptyState.vue';

defineProps({
    title: { type: String, required: true },
    eyebrow: { type: String, default: null },
    description: { type: String, default: null },
    routeName: { type: String, required: true },
    collection: { type: Object, required: true },
    filters: { type: Object, required: true },
});
</script>

<template>
    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p v-if="eyebrow" class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">{{ eyebrow }}</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">{{ title }}</h1>
            <p v-if="description" class="mt-3 max-w-2xl text-muted">{{ description }}</p>
        </div>

        <FilterBar :route-name="routeName" :filters="filters" />

        <p class="mb-6 text-sm text-muted">
            <span class="font-semibold text-ink">{{ collection.total.toLocaleString() }}</span> titles found
        </p>

        <MediaGrid v-if="collection.data.length" :items="collection.data" />
        <EmptyState v-else title="No titles match your filters" description="Try changing the genre, year or search term.">
            <template #icon><FilmIcon class="h-7 w-7" /></template>
        </EmptyState>

        <Pagination :current-page="collection.current_page" :last-page="collection.last_page" />
    </div>
</template>
