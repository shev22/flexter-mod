<template>
    <div class="carousel__wrapper">
        <div class="min-w-[1200px] relative pt-14">
            <div class="flex justify-between mr-6">
                <div class="flex items-center font-semibold text-white text-2xl cursor-pointer">
                 {{ category }}
                </div>
            </div>
            <Carousel v-bind="config">
                <Slide v-for="movie in movies?.data" :key="movie.id" >
                    <img
                        :src="`https://image.tmdb.org/t/p/w500${movie.poster_path}`"
                        alt="image"
                        class="cursor-pointer transition-transform transform hover:scale-105"
                        @click="setActiveMovie(movie)"
                        :class="{ 'border-4 border-white rounded-lg': activeMovie && activeMovie.id === movie.id }"
                    />
                </Slide>
                <template #addons>
                    <Navigation />
                    <CarouselPagination />
                </template>
            </Carousel>
        </div>
    </div>
</template>

<script setup>
import { defineProps, toRefs } from 'vue';
import { Carousel, Slide, Navigation } from 'vue3-carousel';
import { Pagination as CarouselPagination } from 'vue3-carousel'

const props = defineProps({
    movies: Object,
    activeMovie: Object,
    setActiveMovie: Function,
    config: Object,
    category: String
});
console.log(props)


const { movies, activeMovie, setActiveMovie, config } = toRefs(props);
</script>

<style scoped>
</style>
