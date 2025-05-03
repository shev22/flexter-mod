<template>
    <Head :title="' | ' + actor.name" />
    <div class="actor-page bg-gray-900 text-white min-h-screen">
        <ActorHero :actor="actor" />

        <div class="container mx-auto px-4 py-12">
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Left Column -->
                <div class="lg:w-2/3">
                    <BiographySection :biography="actor.biography" />

                    <FilmographySection
                        :content-type="contentType"
                        :movies="actorMovies"
                        :tv="actorTv"
                        @set-content-type="setContentType"
                        @toggle-watchlist="toggleWatchlist"
                        @update-sort="setSortBy"
                        header="Filmography"
                    />
                </div>

                <!-- Right Column -->
                <div class="lg:w-1/3">
                    <PersonalDetails :details="actor" class="mb-8" />
                    <ActorImages :images="actorImages" :name="actor.name" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import {Head, router} from "@inertiajs/vue3";
import { route } from "ziggy-js";
import ActorHero from "../../../Components/Actor/ActorHero.vue";
import ActorImages from "../../../Components/Actor/ActorImages.vue";
import BiographySection from "../../../Components/Actor/BiographySection.vue";
import FilmographySection from "../../../Components/Actor/FilmographySection.vue";
import PersonalDetails from "../../../Components/Actor/PersonalDetails.vue";


const props = defineProps({
    actor: Object,
    images: Array,
    movies: Object,
    tv: Object,
});

const actor = computed(() => props.actor.data);
const actorImages = computed(() => props.images);
const actorMovies = computed(() => props.movies.data);
const actorTv = computed(() => props.tv.data);

// Filter and sorting states
const contentType = ref('Movie');
const sortBy = ref('vote_average');

const setContentType = (type) => {
    contentType.value = type;
};

const setSortBy = (method) => {
    sortBy.value = method;
};

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

</script>
