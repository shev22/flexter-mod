<template>
    <Head title=" | Actors"/>

    <div v-if="actors.length" class="mt-[8rem] mx-[3rem] px-4 md:px-10 ">
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 xl:grid-cols-11 gap-4 ">
            <transition-group name="fade" tag="div" class="contents">
                <div v-for="actor in actors" :key="actor.id">
                    <ActorCard
                        :actor="{
                            id: actor.id,
                            image: 'https://image.tmdb.org/t/p/w185/' + actor.profile_path,
                            name: actor.name,
                        }"
                    />
                </div>
            </transition-group>
        </div>

        <!-- Sentinel Element -->
        <div ref="loadMoreRef" class="h-1 col-span-full"></div>

        <div v-if="loading" class="text-center py-4 text-gray-400 text-sm">
            Loading more...
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import ActorCard from "../../../Components/Actor/ActorCard.vue";

const props = defineProps({
    actors: Object,        // Notice: Object, not Array
    currentPage: Number,
    lastPage: Number,
})

const actors = ref([...props.actors.data])
const currentPage = ref(props.currentPage)
const lastPage = ref(props.lastPage)
const loading = ref(false)
const loadMoreRef = ref(null)

const loadMore = () => {
    if (loading.value || currentPage.value >= lastPage.value) return

    loading.value = true
    router.get(
        route('actors', { page: currentPage.value + 1 }),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            only: ['actors', 'currentPage', 'lastPage'],
            onSuccess: (page) => {
                actors.value.push(...page.props.actors.data)  // ← also use page.props.actors.data here
                currentPage.value = page.props.currentPage
                lastPage.value = page.props.lastPage
                loading.value = false
            },
        }
    )
}

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


<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: all 0.4s ease;
}
.fade-enter-from {
    opacity: 0;
    transform: translateY(10px);
}
.fade-enter-to {
    opacity: 1;
    transform: translateY(0);
}
</style>

