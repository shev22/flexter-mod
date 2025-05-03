<template>
    <div v-if="movie" class=" text-white w-[53%]">

        <img
            class="max-w-[45rem]"
            v-if="movie.logo"
            :src="'https://image.tmdb.org/t/p/original' + movie.logo"
            alt=""
        >

        <div  v-else class="text-[50px] font-semibold font-serif">{{  movie.title }}</div>
        <div class="flex items-center text-lg pt-4 text-cyan-400">
            <!-- IMDB Rating -->
            <div class="flex items-center">
                <span class="bg-yellow-500 text-black px-1 text-xs font-bold rounded">IMDB</span>
                <span class="mx-1 text-[18px]">
            <span class="text-[13px] text-yellow-400">⭐</span>
            {{ movie.vote_average }}
        </span>
            </div>

            <!-- Divider -->
            <span class="mx-0.5 text-[27px]">|</span>

            <!-- Year -->
            <div>{{ movie.year }}</div>

            <!-- Divider -->
            <span class="mx-0.5 text-[27px]">|</span>

            <!-- Type -->
            <div>{{ movie.type }}</div>

            <!-- Divider -->
            <span class="mx-0.5 text-[27px]">|</span>

            <!-- Genre -->
            <div>{{ movie.genre }}</div>
        </div>

        <div class="flex space-x-2 mt-2  pointer-events-auto">
            <!-- Play Button -->
            <Link
                :href="movieUrl"
                class="bg-teal-500 hover:bg-teal-600 px-4 py-2 rounded flex items-center justify-center">
                <span class="text-lg">▶</span>
            </Link>

            <!-- Watchlist Toggle Button -->
            <button
                @click="toggleWatchlist(movie)"
                class="flex items-center justify-center bg-teal-500 hover:bg-teal-600 px-4 py-2 rounded font-bold transition-colors duration-200"
                :class="{'bg-teal-700': inWatchlist, 'hover:bg-teal-800': inWatchlist}"
                :title="inWatchlist ? 'Remove from watchlist' : 'Add to watchlist'"
            >
        <span class="text-lg">
            {{ inWatchlist ? '✓' : '➕ ' }}
        </span>
                <span class="ml-2 text-sm hidden sm:inline">
            {{ inWatchlist ? 'In Watchlist' : 'Watchlist' }}
        </span>
            </button>
        </div>
        <div class="text-xl pt-6">{{ movie.overview }}</div>
    </div>
</template>

<script setup>
import {computed, ref} from "vue";
import {Link, router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps({ movie: Object })

const inWatchlist = ref(props.movie.in_watchlist)

const toggleWatchlist = (item) => {

    const mediaType = item.type === 'Tv' ? 'tv' : 'movie'
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

const generateSlug = (title) => {
    return title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim()
}

const movieUrl = computed(() => {
    const mediaType = props.movie.type === 'Tv' ? 'tv' : 'movie'
    const routeName = `${mediaType}.show`;

    return route(routeName, {
        id: props.movie.id,
        slug: generateSlug(props.movie.title)
    })
})

</script>
