<template>
    <div class="container mx-auto px-4 py-8">
        <Head title=" | Browse"/>

        <h1 class="text-3xl font-bold mb-8">Browse</h1>

        <div class="flex flex-wrap items-center gap-4 mb-6">
            <BrowseFilters
                :media-types="mediaTypes"
                :active-types="activeMediaTypes"
                @toggle-type="toggleMediaType"
            />

            <SortDropdown
                :options="sortOptions"
                :current-sort="currentSort"
                @sort="sortBy"
            />
        </div>

        <BrowseResults
            :loading="loading"
            :results="filteredResults"
            @navigate="navigateToResult"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from "@inertiajs/vue3";
import SortDropdown from "../../../Components/Search/Browse.vue/SortDropdown.vue";
import BrowseResults from "../../../Components/Search/Browse.vue/BrowseResults.vue";
import BrowseFilters from "../../../Components/Search/Browse.vue/BrowseFilters.vue";
import {route} from "ziggy-js";

const props = defineProps({
    results: Object,
    query: String
});

const loading = ref(false);
const showSortDropdown = ref(false);
const activeMediaTypes = ref(['movie', 'tv']);
const currentSort = ref('rating_desc');

const mediaTypes = ref([
    { value: 'movie', label: 'Movies' },
    { value: 'tv', label: 'TV Shows' },
    { value: 'person', label: 'People' }
]);

const sortOptions = ref([
    { value: 'rating_desc', label: 'Rating (High to Low)' },
    { value: 'rating_asc', label: 'Rating (Low to High)' },
    { value: 'year_desc', label: 'Year (Newest First)' },
    { value: 'year_asc', label: 'Year (Oldest First)' }
]);

const filteredResults = computed(() => {
    let results = props.results.data.filter(result =>
        activeMediaTypes.value.includes(result.type)
    );

    // Apply sorting
    switch (currentSort.value) {
        case 'rating_desc':
            return results.sort((a, b) => b.vote_average - a.vote_average);
        case 'rating_asc':
            return results.sort((a, b) => a.vote_average - b.vote_average);
        case 'year_desc':
            return results.sort((a, b) => {
                const dateA = a.release_date || '0';
                const dateB = b.release_date || '0';
                return dateB.localeCompare(dateA);
            });
        case 'year_asc':
            return results.sort((a, b) => {
                const dateA = a.release_date  || '0';
                const dateB = b.release_date  || '0';
                return dateA.localeCompare(dateB);
            });
        default:
            return results;
    }
});

const toggleMediaType = (type) => {
    const index = activeMediaTypes.value.indexOf(type);
    if (index === -1) {
        activeMediaTypes.value.push(type);
    } else {
        activeMediaTypes.value.splice(index, 1);
    }
};

const sortBy = (option) => {
    currentSort.value = option;
    showSortDropdown.value = false;
};


function generateSlug(title) {
    return title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim();
}

const navigateToResult = (result) => {
    window.location.href = route(result.route, {
        id: result.id,
        slug: generateSlug(result.title)
    });
};
</script>
