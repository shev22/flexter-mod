<template>
    <Head title=" | Tv Shows"/>

    <div v-if="tvList.length" class="p-5 mx-10 ">
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-11 gap-3">
            <transition-group name="fade" tag="div" class="contents">
                <div
                    v-for="tv in tvList"
                    :key="tv.id"
                    class="flex justify-center"
                >
                    <TvCard
                        :tv="{
                            id: tv.id,
                            image: 'https://image.tmdb.org/t/p/w185/' + tv.poster_path,
                            title: tv.title,
                            genre: tv.genre,
                            rating: tv.vote_average,
                            in_watchlist: tv.in_watchlist,
                            releaseDate: tv.year
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
            No more shows to load.
        </div>
    </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import TvCard from '../../../Components/Tv/TvCard.vue'

// Props match your controller response
const props = defineProps({
    tv: Object,
    currentPage: Number,
    lastPage: Number,
})

// Local reactive state
const tvList = ref([...props.tv.data])
const currentPage = ref(props.currentPage)
const lastPage = ref(props.lastPage)
const loading = ref(false)
const loadMoreRef = ref(null)


const loadMore = () => {
    if (loading.value || currentPage.value >= lastPage.value) return

    loading.value = true
    router.get(
        route('tv', { page: currentPage.value + 1 }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['tv', 'currentPage', 'lastPage'], // very important!
            onSuccess: (page) => {
                tvList.value.push(...page.props.tv.data)
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


