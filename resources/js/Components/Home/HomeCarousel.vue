<template>
    <Head title=" | Home" />

    <div class="relative h-screen">
        <!-- Carousel as background -->
        <Carousel
            v-bind="config"
            v-model="currentSlide"
            class="full-height-carousel"
        >
            <Slide v-for="(image, index) in images" :key="image.id">
                <div
                    class="relative w-full h-full"
                    @mouseenter="index === currentSlide && onMouseEnter()"
                    @mouseleave="index === currentSlide && onMouseLeave()"
                >
                    <!-- Background image -->
                    <transition name="fade">
                        <img
                            v-if="!hovered || index !== currentSlide || !hasTrailer"
                            :src="image.url"
                            alt="image"
                            class="absolute inset-0 w-full h-full object-cover z-0"
                        />
                    </transition>
                    <!-- YouTube iframe -->
                    <transition name="fade">
                        <iframe
                            v-if="hovered && index === currentSlide && hasTrailer"
                            class="zoomed-iframe"
                            :src="`https://www.youtube.com/embed/${image.trailer}?autoplay=1&loop=1&mute=1&controls=0&showinfo=0&modestbranding=1&rel=0&enablejsapi=1&playlist=${image.trailer}`"
                            frameborder="0"
                            allow="autoplay; encrypted-media"
                            allowfullscreen
                            ref="iframeRef"
                        >
                        </iframe>
                    </transition>
                </div>
            </Slide>

            <template #addons>
                <Navigation />
            </template>
        </Carousel>

        <!-- Overlay with movie details -->
        <div class="absolute inset-0 flex z-10 px-8 md:px-16 pointer-events-none ml-20 mt-36" >
            <transition name="slide-fade" mode="out-in">
                <HomeMovieDetails
                    v-if="movies"
                    :movie="currentMovie"
                    :key="currentMovie?.id"
                    class="movie-details-animation"
                />
            </transition>
        </div>
    </div>

    <div class=" w-3/5 mx-auto my-20 border-gray-800">
            <HomeNowPlaying
                :movies="movies.nowPlayingMovies"
                :tv="movies.airingToday"
            />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3"
import 'vue3-carousel/carousel.css'
import { Carousel, Slide, Navigation } from 'vue3-carousel'
import { ref, computed } from 'vue'
import HomeMovieDetails from "./HomeMovieDetails.vue";
import FilmographySection from "../Actor/FilmographySection.vue";
import HomeNowPlaying from "./HomeNowPlaying.vue";

const props = defineProps({
    movies: Object
})

const currentSlide = ref(0)
const hovered = ref(false)
const iframeRef = ref(null)

const images = props.movies.trending.data.map((movie, index) => ({
    id: movie.id || index,
    url: `https://image.tmdb.org/t/p/w1280/${movie.backdrop_path}`,
    trailer: movie.trailer
}))

const currentMovie = computed(() => {
    return props.movies?.trending?.data[currentSlide.value]
})

const hasTrailer = computed(() => {
    return currentMovie.value?.trailer !== null
})

const config = {
    height: 1000,
    itemsToShow: 1,
    gap: 5,
    autoplay: 6000,
    wrapAround: true,
    pauseAutoplayOnHover: true
}

function onMouseEnter() {
    hovered.value = true
    postMessageToIframe('playVideo')
}

function onMouseLeave() {
    hovered.value = false
    postMessageToIframe('pauseVideo')
}

function postMessageToIframe(action) {
    const iframe = iframeRef.value
    if (!iframe || !iframe.contentWindow) return

    iframe.contentWindow.postMessage(
        JSON.stringify({
            event: 'command',
            func: action,
            args: []
        }),
        '*'
    )
}
























</script>

<style scoped>
img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carousel {
    --vc-nav-background: white;
    --vc-nav-border-radius: 50%;
}

.full-height-carousel::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.1) 100%);

    pointer-events: none;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

.slide-fade-enter-active, .slide-fade-leave-active {
    transition: all 0.5s ease;
}
.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(100%);
}
.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(-100%);
}

.zoomed-iframe {
    width: 120%;
    height: 120%;
    position: absolute;
    top: -10%;
    left: -10%;
    transform: scale(1.2);
    transform-origin: center center;
    z-index: 0;
    border: none;
    pointer-events: none; /* Prevents weird iframe interactions unless needed */
}
</style>

