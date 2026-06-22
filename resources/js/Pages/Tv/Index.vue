<script setup>
import { Head } from '@inertiajs/vue3';
import MediaLibrary from '../../Components/media/MediaLibrary.vue';

defineProps({
    shows: { type: Object, required: true },
    filters: { type: Object, required: true },
});
</script>

<template>
    <Head title="Series & TV" />
    <MediaLibrary
        title="Series & TV"
        eyebrow="Binge worthy"
        description="Discover series and shows from every genre. Filter, sort and add to your watchlist."
        route-name="tv"
        :collection="shows"
        :filters="filters"
    />
</template>
