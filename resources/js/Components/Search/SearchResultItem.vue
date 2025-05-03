<template>
    <div
        class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-700 last:border-b-0"
        @click="$emit('navigate', result)"
    >
        <div class="flex items-center space-x-3">
            <!-- Poster Image -->
            <div class="flex-shrink-0">
                <img
                    :src="result.poster_path ? `https://image.tmdb.org/t/p/w92${result.poster_path}` : '/https://placehold.co/600x400'"
                    :alt="result.title"
                    class="w-14 h-16 object-cover rounded"
                />
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-baseline">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ result.title }}
                    </h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
            {{ formatMediaType(result.type) }}
          </span>
                </div>

                <!-- Rating -->
                <div class="flex items-center mt-1">
                    <div class="flex items-center">
                        <svg
                            v-for="star in 5"
                            :key="star"
                            class="w-3 h-3"
                            :class="star <= Math.round(result.vote_average / 2) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500'"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
              {{ result.vote_average.toFixed(1) }}
            </span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mx-2">•</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ result.release_date ? result.release_date.substring(0, 4)  : 'N/A' }}
          </span>
                </div>

                <!-- Overview -->
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                    {{ result.overview }}
                </p>
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
        tv: 'TV Browse.vue',
        person: 'Person'
    };
    return types[type] || type;
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
