<template>
    <section>
        <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-700">
            <h2 class="text-2xl font-bold">{{header}}</h2>
            <div class="flex gap-2 items-center">
                <button
                    @click="emit('set-content-type', 'Movie')"
                    class="px-4 py-1 rounded transition-colors"
                    :class="contentType === 'Movie' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-800'"
                >
                    Movies
                </button>
                <button
                    @click="emit('set-content-type', 'TV')"
                    class="px-4 py-1 rounded transition-colors"
                    :class="contentType === 'TV' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-800'"
                >
                    TV Shows
                </button>

                <div class="relative filter-menu">
                    <button
                        @click.stop="toggleSortMenu"
                        class="p-2 bg-gray-700 rounded hover:bg-gray-600 transition flex items-center"
                    >
                        <i class="fas fa-filter text-lg"></i>
                    </button>

                    <div
                        v-show="showSortMenu"
                        class="absolute right-0 top-full mt-2 bg-gray-800 rounded-lg shadow-lg z-20 w-36"
                    >
                        <div class="p-2 font-semibold border-b border-gray-700 text-center">Sort By</div>
                        <div class="py-1">
                            <button
                                @click="setSortBy('vote_average')"
                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition"
                                :class="sortBy === 'vote_average' ? 'text-yellow-400' : ''"
                            >
                                Rating
                            </button>
                            <button
                                @click="setSortBy('year')"
                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition"
                                :class="sortBy === 'year' ? 'text-yellow-400' : ''"
                            >
                                Year
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-6 max-h-[65rem] overflow-y-auto pb-4">
            <MediaCard
                v-for="item in filteredAndSortedContent"
                :key="item.id"
                :item="item"
                @toggle-watchlist="emit('toggle-watchlist', item)"
            />
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import MediaCard from './MediaCard.vue';

const props = defineProps({
    contentType: {
        type: String,
        default: 'Movie'
    },
    sortBy: {
        type: String,
        default: 'vote_average'
    },
    movies: {
        type: Array,
        default: () => []
    },
    tv: {
        type: Array,
        default: () => []
    },
    header: {
        type: String,
    }
});

const emit = defineEmits(['set-content-type', 'toggle-watchlist', 'update-sort']);

const showSortMenu = ref(false);

const toggleSortMenu = () => {
    showSortMenu.value = !showSortMenu.value;
};

const setSortBy = (method) => {
    emit('update-sort', method);
    showSortMenu.value = false;
};

const filteredAndSortedContent = computed(() => {
    const content = props.contentType === 'Movie' ? props.movies : props.tv;

    if (!content) return [];

    return [...content].sort((a, b) => {
        if (props.sortBy === 'vote_average') {
            return b.vote_average - a.vote_average;
        } else if (props.sortBy === 'year') {
            return b.year - a.year;
        }
        return 0;
    });
});
</script>
