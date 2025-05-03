<template>
    <Head :title="' | ' + tvData.title" />
    <div class="bg-gray-900 text-white">
        <div class="relative h-screen">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>
                <iframe
                    v-if="tvData.trailer"
                    class="object-cover zoomed-iframe"
                    :src="`https://www.youtube.com/embed/${tvData.trailer}?autoplay=1&loop=1&mute=1&controls=0&showinfo=0&modestbranding=1&rel=0&enablejsapi=1`"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                ></iframe>
                <img v-else :src="backdropPath" class="w-full h-full object-cover opacity-30" />
            </div>

            <ShowMediaHeader
                :media="tvData"
                :inWatchlist="inWatchlist"
                :subtitle="`${tvData.seasons} Seasons`"
                @play="showModal = true"
                @toggleWatchlist="toggleWatchlist"
            />

            <div class="absolute bottom-0 left-0 w-full px-[5rem] z-20 ">
                <h2 class="text-2xl font-semibold mb-4">Related Tv shows</h2>
                <Carousel :items-to-show="12" :wrap-around="true" :autoplay="2000" :transition="500" :pause-autoplay-on-hover="true">
                    <Slide v-for="show in tvData.related" :key="show.id">
                        <div class="flex-shrink-0 w-40 px-3">
                            <TvCard :tv="{
                                id: show.id,
                                image: 'https://image.tmdb.org/t/p/w185/' + getPoster(show.poster_path),
                                title: show.title,
                                genre: show.genre,
                                rating: show.vote_average,
                                in_watchlist: show.in_watchlist,
                                releaseDate: show.year
                            }" />
                            <p class="text-center text-sm mt-2">{{ show.title }}</p>
                        </div>
                    </Slide>
                </Carousel>
            </div>

            <Modal :isOpen="showModal" @close="showModal = false">
                <iframe
                    :src="embedUrl"
                    class="w-full h-[70vh] rounded-sm"
                    frameborder="0"
                    allowfullscreen
                    allow="autoplay; encrypted-media"
                ></iframe>
            </Modal>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Carousel, Slide } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';
import TvCard from '../../../Components/Tv/TvCard.vue';
import Modal from '../../../Components/Media/Modal.vue';
import ShowMediaHeader from '../../../Components/Media/ShowMediaHeader.vue';
import {Head, router} from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const showModal = ref(false);
const props = defineProps({ tv: Object });
const tvData = computed(() => props.tv.data);
const inWatchlist = ref(tvData.value.in_watchlist);

const embedUrl = computed(() => `https://vidsrc.to/embed/tv/${tvData.value.id}`);
const backdropPath = computed(() => tvData.value.backdrop_path ? `https://image.tmdb.org/t/p/original/${tvData.value.backdrop_path}` : 'https://via.placeholder.com/1200x600');

const getPoster = (path) => path ? path : 'https://via.placeholder.com/150x220';

const toggleWatchlist = () => {
    const routeName = inWatchlist.value ? 'tv.remove.watchlist' : 'tv.add.watchlist';
    router.post(route(routeName), { id: tvData.value.id }, {
        preserveScroll: true,
        onSuccess: () => (inWatchlist.value = !inWatchlist.value),
        onError: (errors) => console.error('Watchlist error:', errors),
    });
};
</script>
<style scoped>
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
    pointer-events: none;
}

::-webkit-scrollbar {
    height: 8px;
}
::-webkit-scrollbar-thumb {
    background-color: #4b5563;
    border-radius: 4px;
}
</style>
