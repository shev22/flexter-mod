<template>
    <Head title=" | Movies"/>
    <h1>Movies</h1>
    <div v-if="movieList.length" class="p-5 mx-10">
        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-9 lg:grid-cols-10 xl:grid-cols-12 gap-3">
            <transition-group name="fade" tag="div" class="contents">
                <div
                    v-for="movie in movieList"
                    :key="movie.id"
                    class="flex justify-center"
                >
                    <MovieCard
                        :movie="{
                            id: movie.id,
                            image: 'https://image.tmdb.org/t/p/w185/' + movie.poster_path,
                            title: movie.title,
                            genre: movie.genre,
                            rating: movie.vote_average,
                            in_watchlist: movie.in_watchlist,
                            releaseDate: movie.year
                        }"
                    />
                </div>
            </transition-group>
        </div>

        <!-- Sentinel div for infinite scroll -->
        <div ref="loadMoreRef" class="h-1 col-span-full"></div>

        <!-- Loading / Finished Text -->
        <div v-if="loading" class="text-center py-4 text-gray-400 text-sm">
            Loading more...
        </div>
        <div v-else-if="currentPage >= lastPage" class="text-center py-4 text-gray-400 text-sm">
            No more movies to load.
        </div>
    </div>


</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import MovieCard from '../../../Components/Movie/MovieCard.vue'

// Props match your controller response
const props = defineProps({
    movies: Object,          // movies.data is the movie array
    currentPage: Number,     // current page number
    lastPage: Number,        // last page number
})

// Local reactive state
const movieList = ref([...props.movies.data])
const currentPage = ref(props.currentPage)
const lastPage = ref(props.lastPage)
const loading = ref(false)
const loadMoreRef = ref(null)

// Load more movies when reaching the sentinel
const loadMore = () => {
    if (loading.value || currentPage.value >= lastPage.value) return

    loading.value = true
    router.get(
        route('movies', { page: currentPage.value + 1 }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['movies', 'currentPage', 'lastPage'], // very important!
            onSuccess: (page) => {
                movieList.value.push(...page.props.movies.data)
                currentPage.value = page.props.currentPage
                lastPage.value = page.props.lastPage
                loading.value = false
            },
        }
    )
}

// Intersection Observer to detect scrolling near bottom
const createObserver = () => {
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) loadMore()
    }, { threshold: 1.0 })

    if (loadMoreRef.value) observer.observe(loadMoreRef.value)
}

onMounted(() => {
    createObserver()
})
</script>


