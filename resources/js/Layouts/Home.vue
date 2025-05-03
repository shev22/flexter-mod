
<template>
    <div class="fixed w-full h-screen bg-black">

        <div v-if="!showFullVideo" id="SideNav" class="flex z-40 items-center w-[120px] h-screen bg-black relative">
            <img class="absolute top-0 w-[35px] mt-10 ml-10" src="/images/netflix-logo.png" alt="">
            <div>
                <div class="py-2 mx-10 my-6">
                    <Magnify fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>
                </div>
                <div class="py-2 mx-10 my-6 border-b-4 border-b-red-500">
                    <HomeOutline fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>
                </div>
                <div class="py-2 mx-10 my-6">
                    <TrendingUp fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>
                </div>
                <div class="py-2 mx-10 my-6">
                    <Television fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>
                </div>
                <div class="py-2 mx-10 my-6">
                    <MovieOutline fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>
                </div>
                <div class="py-2 mx-10 my-6">
                    <Plus fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>
                </div>
            </div>
        </div>

        <div v-if="!showFullVideo">
            <div class="fixed flex z-20 top-0 right-0 w-full h-[50%] bg-black pl-[120px] bg-clip-border">
                <div class="absolute z-30 h-[600px] left-[120px] w-[77%] right-0 top-0 bg-gradient-to-r from-black via-black" />

                <HomeMovieDetails v-if="activeMovie" :movie="activeMovie" />

<!--                <video-->
<!--                    src="https://www.w3schools.com/html/mov_bbb.mp4"-->
<!--                    autoplay-->
<!--                    loop-->
<!--                    class="absolute z-0 h-[600px] right-0 top-0"-->
<!--                />-->

                <iframe
                    class="absolute z-0 h-[550px] w-[1000px]  right-0"
                    src="https://www.youtube.com/embed/kyWafhzs6zw?autoplay=1&mute=1&loop=1"
                    allowfullscreen>
                </iframe>
            </div>

            <div class="fixed z-30 bottom-0 right-0 w-full h-[55%] pl-[120px] overflow-y-auto ">
                <div class="fixed z-30 bottom-0 right-0 w-full h-[55%] pl-[120px] overflow-y-auto scrollbar-hide">
                    <VideoCarousel
                        category="Nowplaying Movies"
                        class=" pt-14"
                        :movies="movies.nowPlayingMovies"
                        :activeMovie="activeMovie"
                        :setActiveMovie="setActiveMovie"
                        :config="config"
                    />

                    <VideoCarousel
                        category="Popular Movies"
                        :movies="movies.popularMovies"
                        :activeMovie="activeMovie"
                        :setActiveMovie="setActiveMovie"
                        :config="config"
                    />

                    <VideoCarousel
                        category="Toprated Movies"
                        :movies="movies.topRatedMovies"
                        :activeMovie="activeMovie"
                        :setActiveMovie="setActiveMovie"
                        :config="config"
                    />

                    <VideoCarousel
                        category="upcoming Movies"
                        :movies="movies.upcomingMovies"
                        :activeMovie="activeMovie"
                        :setActiveMovie="setActiveMovie"
                        :config="config"
                    />
            </div>
            </div>
            <div class="absolute z-20 h-[70%] left-[120px] w-[100%] right-0 bottom-0 bg-gradient-to-t from-black via-black" />
        </div>
    </div>
</template>


<script setup>
import { ref, watchEffect, toRefs } from 'vue';
import Magnify from 'vue-material-design-icons/Magnify.vue';
import HomeOutline from 'vue-material-design-icons/HomeOutline.vue';
import TrendingUp from 'vue-material-design-icons/TrendingUp.vue';
import Television from 'vue-material-design-icons/Television.vue';
import MovieOutline from 'vue-material-design-icons/MovieOutline.vue';
import Plus from 'vue-material-design-icons/Plus.vue';
import 'vue3-carousel/dist/carousel.css';
import VideoCarousel from "../Components/VideoCarousel.vue";
import HomeMovieDetails from "../Components/Home/HomeMovieDetails.vue";


const props = defineProps({ movies: Object });
const { movies } = toRefs(props);

const activeMovie = ref(null);

const setActiveMovie = (movie) => {
    activeMovie.value = movie;
};
console.log(movies.value)
watchEffect(() => {
    if (movies.value?.popularMovies.data?.length) {
        activeMovie.value = movies.value.popularMovies.data[0];
    }
});

const config = {
    autoplay:1,
    wrapAround:1,
    transition:10000,
    height: 220,
    gap: 5,
    snapAlign: 'center',
    breakpointMode: 'carousel',
    breakpoints: {
        300: { itemsToShow: 5, snapAlign: 'center' },
        400: { itemsToShow: 10, snapAlign: 'start' },
        500: { itemsToShow: 13, snapAlign: 'start' },
    },
};

</script>

