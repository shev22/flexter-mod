<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div
            @click="$emit('navigate', result)"
            class="p-6 hover:cursor-pointer"
        >
            <div class="flex items-start gap-6">
                <!-- Poster Image -->
                <img
                    :src="result.poster_path ? `https://image.tmdb.org/t/p/w200${result.poster_path}` : '/placeholder.jpg'"
                    :alt="result.title"
                    class="w-24 h-32 object-cover rounded"
                />

                <!-- Content -->
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ result.title }}
                        </h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
              {{ result.release_date ? result.release_date.substring(0, 4) : 'N/A' }}
            </span>
                    </div>

                    <!-- Media Type and Rating -->
                    <div class="flex items-center mt-2 space-x-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">
              {{ formatMediaType(result.type) }}
            </span>
                        <div class="flex items-center">
                            <svg
                                v-for="star in 5"
                                :key="star"
                                class="w-4 h-4"
                                :class="star <= Math.round(result.vote_average / 2) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500'"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">
                {{ result.vote_average.toFixed(1) }}/10
              </span>
                        </div>
                    </div>

                    <!-- Genres -->
                    <div class="mt-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">
              {{ result.genres ? result.genres.join(', ') : '' }}
            </span>
                    </div>

                    <!-- Overview -->
                    <p class="mt-3 text-gray-600 dark:text-gray-300 line-clamp-3">
                        {{ result.overview }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    result: {
        type: Object,
        required: true
    }
});

defineEmits(['navigate']);

const formatMediaType = (type) => {
    const types = {
        movie: 'Movie',
        tv: 'TV Show',
        person: 'Person'
    };
    return types[type] || type;
};
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
