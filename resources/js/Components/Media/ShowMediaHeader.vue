/* --- Reusable component file: MediaHeader.vue --- */
<template>
    <div class="absolute top-[-6rem] left-28 w-full h-full flex flex-col justify-center px-16 z-20">
        <div class="flex">
            <img
                :src="`https://image.tmdb.org/t/p/w185/${media.poster_path}`"
                class="w-[10.5rem] h-[13rem] rounded-lg mr-3"
                :alt="media.title"
            >

            <div class="flex flex-col justify-center">
                <img
                    class="max-w-[10rem]"
                    v-if="media.logo"
                    :src="`https://image.tmdb.org/t/p/original${media.logo}`"
                    :alt="media.title"
                >
                <h1 v-else class="text-5xl font-bold font-serif">{{ media.title }}</h1>

                <p class="text-teal-200 mt-0.5">{{ subtitle }}</p>

                <div class="flex items-center space-x-2">
                    <p class="text-teal-200">{{ media.year }} |</p>
                    <div class="text-yellow-400 text-xl">{{ starRating }}</div>
                </div>

                <div class="flex space-x-4 mt-0.5 text-teal-200">
                    {{ media.genres }}
                </div>
            </div>
        </div>

        <div class="flex space-x-2 mt-2">
            <button
                @click="$emit('play')"
                class="bg-teal-500 hover:bg-teal-600 px-4 py-2 rounded flex items-center justify-center">
                <span class="text-lg">▶</span>
            </button>

            <button
                @click="$emit('toggleWatchlist')"
                class="flex items-center justify-center bg-teal-500 hover:bg-teal-600 px-4 py-2 rounded font-bold transition-colors duration-200"
                :class="{'bg-teal-700': inWatchlist, 'hover:bg-teal-800': inWatchlist}"
                :title="inWatchlist ? 'Remove from watchlist' : 'Add to watchlist'"
            >
                <span class="text-lg">{{ inWatchlist ? '✓' : '➕' }}</span>
                <span class="ml-2 text-sm hidden sm:inline">
                    {{ inWatchlist ? 'In Watchlist' : 'Watchlist' }}
                </span>
            </button>
        </div>

        <p class="mt-3 text-gray-300 max-w-[85%] text-[17px]">
            {{ media.overview }}
        </p>

        <div v-if="media.cast" class="mt-6">
            <h3 class="text-2xl font-bold">Cast</h3>
            <div class="flex flex-wrap gap-2">
                <a
                    v-for="actor in media.cast"
                    :key="actor.id"
                    :href="actorUrl(actor)"
                    class="text-teal-400 hover:text-teal-300 text-[17px]"
                >
                    {{ actor.name }}
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    media: Object,
    inWatchlist: Boolean,
    subtitle: String,
});

const actorUrl = (actor) => {
    const slug = actor.name.toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').trim();
    return route('actor.show', { id: actor.id, slug });
};

const starRating = computed(() => {
    const rating = props.media.vote_average || 0;
    const fullStars = Math.floor(rating / 2);
    const emptyStars = 5 - fullStars;
    return '★'.repeat(fullStars) + '☆'.repeat(emptyStars);
});
</script>
