<template>
    <FilmographySection
        :content-type="contentType"
        :movies="movies"
        :tv="tv"
        @set-content-type="setContentType"
        @toggle-watchlist="toggleWatchlist"
        header="Now playing"
    />
</template>

<script setup>
import {computed, ref} from "vue";
import FilmographySection from "../Actor/FilmographySection.vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps({
    movies: Object,
    tv: Object
})

const movies = computed(() => props.movies.data);
const tv = computed(() => props.tv.data);

// Filter and sorting states
const contentType = ref('Movie');

const toggleWatchlist = (item) => {
    const mediaType = item.type === 'Tv' ? 'tv' : 'movie';
    const action = item.in_watchlist ? 'remove' : 'add';
    const routeName = `${mediaType}.${action}.watchlist`;

    router.post(
        route(routeName),
        { id: item.id },
        {
            preserveScroll: true,
            onSuccess: () => {
                item.in_watchlist = !item.in_watchlist;
            },
            onError: (errors) => {
                console.error('Failed to update watchlist:', errors);
            },
        }
    );
};

const setContentType = (type) => {
    contentType.value = type;
};
</script>
