<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { FilmIcon, TvIcon, QueueListIcon } from '@heroicons/vue/24/solid';
import HeroCarousel from '../Components/home/HeroCarousel.vue';
import Rail from '../Components/ui/Rail.vue';
import MediaCard from '../Components/ui/MediaCard.vue';
import ListIcon from '../Components/ui/ListIcon.vue';
import ContinueWatchingCard from '../Components/ui/ContinueWatchingCard.vue';
import SkeletonCard from '../Components/ui/SkeletonCard.vue';

defineProps({
    hero: { type: Array, default: () => [] },
    continueWatching: { type: Array, default: () => [] },
    recommendations: { type: Array, default: () => [] },
    actorFeed: { type: Array, default: () => [] },
    featuredLists: { type: Array, default: () => [] },
    movieRails: { type: Array, default: () => [] },
    tvRails: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
});
</script>

<template>
    <Head title="Home" />

    <HeroCarousel :items="hero" />

    <div class="space-y-16 px-4 py-14 lg:px-12">
        <section v-if="continueWatching.length">
            <Rail title="Continue watching" eyebrow="Pick up where you left off">
                <ContinueWatchingCard v-for="item in continueWatching" :key="`${item.type}-${item.id}`" :item="item" />
            </Rail>
        </section>

        <section v-if="recommendations.length || loading">
            <Rail title="Recommended for you" eyebrow="Based on your taste">
                <template v-if="loading">
                    <SkeletonCard v-for="n in 48" :key="`rec-${n}`" />
                </template>
                <MediaCard v-for="item in recommendations" v-else :key="`${item.type}-${item.id}`" :item="item" />
            </Rail>
        </section>

        <section v-if="actorFeed.length">
            <Rail title="From actors you follow" eyebrow="Fresh from your favourites">
                <MediaCard v-for="item in actorFeed" :key="`actor-${item.type}-${item.id}`" :item="item" />
            </Rail>
        </section>

        <section v-if="featuredLists.length">
            <div class="mb-8 flex items-center gap-4">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-aurora text-white shadow-glow">
                    <QueueListIcon class="h-6 w-6" />
                </span>
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Curated picks</p>
                    <h2 class="font-display text-2xl font-extrabold text-ink sm:text-3xl">Featured lists</h2>
                </div>
                <Link :href="route('lists')" class="rounded-full glass px-4 py-2 text-sm font-medium text-ink transition hover:text-accent">Browse all</Link>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                <Link
                    v-for="list in featuredLists"
                    :key="list.slug"
                    :href="route('lists.show', list.slug)"
                    class="group rounded-2xl glass p-4 transition hover:ring-1 hover:ring-accent/40"
                >
                    <div class="mb-3 flex items-start gap-2.5">
                        <ListIcon :icon="list.icon" size="sm" />
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="min-w-0 truncate font-display text-sm font-bold text-ink group-hover:text-accent sm:text-base">
                                    {{ list.title }}
                                </h3>
                                <span class="shrink-0 text-[10px] font-semibold tabular-nums text-accent sm:text-xs">
                                    {{ list.item_count ?? list.items.length }}
                                </span>
                            </div>
                            <p v-if="list.description" class="mt-1 line-clamp-2 text-xs italic text-muted">
                                {{ list.description }}
                            </p>
                        </div>
                    </div>
                    <div class="no-scrollbar flex gap-1 overflow-x-auto pb-0.5">
                        <img
                            v-for="(item, idx) in list.items"
                            :key="`${item.type}-${item.id}-${idx}`"
                            :src="item.poster"
                            :alt="item.title"
                            class="h-14 w-9 shrink-0 rounded object-cover ring-1 ring-hair/10 transition group-hover:-translate-y-0.5 sm:h-16 sm:w-10"
                        />
                    </div>
                </Link>
            </div>
        </section>

        <div>
            <div class="mb-8 flex items-center gap-4">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-aurora text-white shadow-glow">
                    <FilmIcon class="h-6 w-6" />
                </span>
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">The big screen</p>
                    <h2 class="font-display text-2xl font-extrabold text-ink sm:text-3xl">Movies</h2>
                </div>
                <Link :href="route('movies')" class="rounded-full glass px-4 py-2 text-sm font-medium text-ink transition hover:text-accent">Explore all</Link>
            </div>
            <div class="space-y-10">
                <Rail v-for="rail in movieRails" :key="rail.title" :title="rail.title" :href="route('movies')">
                    <MediaCard v-for="item in rail.items" :key="item.id" :item="item" />
                </Rail>
            </div>
        </div>

        <div class="rounded-4xl bg-aurora-soft p-5 sm:p-8">
            <div class="mb-8 flex items-center gap-4">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-aurora text-white shadow-glow">
                    <TvIcon class="h-6 w-6" />
                </span>
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Binge worthy</p>
                    <h2 class="font-display text-2xl font-extrabold text-ink sm:text-3xl">Series &amp; TV</h2>
                </div>
                <Link :href="route('tv')" class="rounded-full glass px-4 py-2 text-sm font-medium text-ink transition hover:text-accent">Explore all</Link>
            </div>
            <div class="space-y-10">
                <Rail v-for="rail in tvRails" :key="rail.title" :title="rail.title" :href="route('tv')">
                    <MediaCard v-for="item in rail.items" :key="item.id" :item="item" />
                </Rail>
            </div>
        </div>
    </div>
</template>
