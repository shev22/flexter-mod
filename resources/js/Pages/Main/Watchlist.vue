<template>
    <Head title=" | Watch list"/>
    <h1>Watchlist</h1>


    <div  class="p-5 mt-20 mx-12">
        <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-9 lg:grid-cols-10 xl:grid-cols-12 gap-3">
            <div v-for="movie in media.movies.data" :key="movie.id"  class="flex justify-center">
                <MovieCard
                    :movie="{
                    id: movie.id,
                    image: 'https://image.tmdb.org/t/p/w500/' + movie.poster_path,
                    title: movie.title,
                    genre: movie.genre,
                    rating: movie.vote_average,
                    in_watchlist: movie.in_watchlist,
                    releaseDate: movie.year
                  }"
                />

            </div>

            <div v-for="tv in media.tv.data" :key="tv.id"  class="flex justify-center">
                <TvCard
                    :tv="{
                    id: tv.id,
                    image: 'https://image.tmdb.org/t/p/w500/' + tv.poster_path,
                    title: tv.title,
                    genre: tv.genre,
                    rating: tv.vote_average,
                    in_watchlist: tv.in_watchlist,
                    releaseDate: tv.year
                  }"
                />
            </div>
        </div>

    </div>


</template>

<script setup>
import {Head} from "@inertiajs/vue3";
import MovieCard from "../../Components/Movie/MovieCard.vue";
import TvCard from "../../Components/Tv/TvCard.vue";
const media = defineProps({
    movies: Object,
    tv: Object
})

</script>


