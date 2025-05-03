<template>
    <div class="relative rounded-lg overflow-hidden shadow-lg group bg-black">

        <Link :href="tvUrl" class="block w-full h-full">

            <img
                :src="tv.image"
                :alt="tv.title + ' Poster'"
                class="w-full h-full object-cover"
            />

            <!-- Hover overlay -->
            <div class="absolute inset-0 flex flex-col justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="h-1/6 bg-gradient-to-t from-black/80 to-transparent"></div>

                <div class="w-full bg-black/80 px-2 pb-1">
                    <h3 class="text-white font-semibold text-sm transition-all duration-300 group-hover:translate-y-0 translate-y-2 opacity-0 group-hover:opacity-100">
                        {{ tv.title }}
                    </h3>
                    <p class="text-xs text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        {{ tv.genre }}
                    </p>
                    <div class="flex items-center justify-between text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="flex items-center space-x-1">
                            <span class="bg-yellow-500 text-black px-1 rounded font-bold">IMDB</span>
                            <span class=" text-yellow-400 px-1 text-[8px]">⭐</span>{{ tv.rating }}
                        </div>
                        |
                        <span class="text-gray-300">{{ tv.releaseDate }}</span>
                    </div>
                </div>
            </div>
        </Link>

        <!-- Watchlist Toggle Button -->
        <!-- Watchlist Toggle Button -->
        <div
            role="button"
            :aria-label="inWatchlist ? 'Remove from watchlist' : 'Add to watchlist'"
            tabindex="0"
            class="absolute top-2 right-2 text-white rounded cursor-pointer transition active:scale-95 z-10 group/button"
            :class="inWatchlist ? 'bg-red-700/90 hover:bg-gray-700/90' : 'bg-green-800/90 hover:bg-gray-700/90'"
            @click="toggleWatchlist"
        >
            <button v-if="inWatchlist" class="bg-teal-700/70 hover:bg-teal-800 px-2  rounded">x</button>

            <button v-else class="bg-teal-700/70 hover:bg-teal-800 px-2  rounded">+</button>

            <!-- Tooltip (only shows on hover of this button) -->
            <span
                class="absolute top-full right-0 mt-1 bg-black/60 text-white text-xs rounded px-2 py-1 opacity-0 group-hover/button:opacity-100 transition-opacity whitespace-nowrap z-10"
            >
        {{ inWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist' }}
    </span>
        </div>

    </div>
</template>


<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
    tv: {
        type: Object,
        required: true,
    },
})

const inWatchlist = ref(props.tv.in_watchlist)

const tvUrl = computed(() => {
    return route('tv.show', {
        id: props.tv.id,
        slug: generateSlug(props.tv.title)
    })
})

function generateSlug(title) {
    return title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim()
}

const toggleWatchlist = () => {
    const routeName = inWatchlist.value ? 'tv.remove.watchlist' : 'tv.add.watchlist'

    router.post(
        route(routeName),
        { id: props.tv.id },
        {
            preserveScroll: true,
            onSuccess: () => {
                inWatchlist.value = !inWatchlist.value
            },
            onError: (errors) => {
                console.error('Failed to update watchlist:', errors)
            },
        }
    )
}
</script>
