<script setup>
import { Head } from '@inertiajs/vue3';
import MediaLibrary from '../../Components/media/MediaLibrary.vue';

defineProps({
    movies: { type: Object, required: true },
    filters: { type: Object, required: true },
});
</script>

<template>
    <Head title="Movies" />
    <MediaLibrary
        title="Movies"
        eyebrow="The big screen"
        description="Browse the full library of films. Filter by genre, release year, rating and more."
        route-name="movies"
        :collection="movies"
        :filters="filters"
    />
</template>
