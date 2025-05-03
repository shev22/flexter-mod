<template>
    <div class="movie-card">
        <a
            :href="url"
            class="relative block cursor-pointer group"
        >
            <div
                role="button"
                :aria-label="item.in_watchlist ? 'Remove from watchlist' : 'Add to watchlist'"
                tabindex="0"
                class="absolute top-2 right-2 text-white rounded cursor-pointer transition active:scale-95 z-10 group/button"
                :class="item.in_watchlist ? 'bg-red-700/90 hover:bg-gray-700/90' : 'bg-green-800/90 hover:bg-gray-700/90'"
                @click.stop.prevent="$emit('toggle-watchlist', item)"
            >
                <button v-if="item.in_watchlist" class="bg-teal-700/70 hover:bg-teal-800 px-2 rounded">x</button>
                <button v-else class="bg-teal-700/70 hover:bg-teal-800 px-2 rounded">+</button>

                <span
                    class="absolute top-full right-0 mt-1 bg-black/60 text-white text-xs rounded px-2 py-1 opacity-0 group-hover/button:opacity-100 transition-opacity whitespace-nowrap z-10"
                >
          {{ item.in_watchlist ? 'Remove from Watchlist' : 'Add to Watchlist' }}
        </span>
            </div>

            <div class="relative pb-[150%] bg-gray-800 rounded-lg overflow-hidden mb-2 group-hover:scale-105 transition-transform">
                <div class="absolute inset-0 bg-gray-700 flex items-center justify-center">
                    <img
                        :src="item.poster_path ? 'https://image.tmdb.org/t/p/w185/' + item.poster_path : 'https://placehold.co/300x400'"
                        :alt="item.title || item.name"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            <div>
                <h3 class="font-semibold truncate">{{ item.title || item.name }}</h3>
                <p class="text-gray-400 text-sm">{{ item.year }}</p>
                <p class="text-gray-400 text-sm">{{ item.genre }}</p>
                <div class="flex items-center mt-1">
                    <span class="text-yellow-400 text-sm mr-1">★</span>
                    <span class="text-sm">{{ item.vote_average }}/10</span>
                </div>
            </div>
        </a>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import {route} from "ziggy-js";

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['toggle-watchlist']);

const url = computed(() => {
    const routeName = props.item.type === 'Tv' ? 'tv.show' : 'movie.show';
    return route(routeName, {
        id: props.item.id,
        slug: generateSlug(props.item.title || props.item.name),
    });
});

const generateSlug = (title) => {
    return title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim();
};
</script>
