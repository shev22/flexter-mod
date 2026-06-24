<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PlayIcon, StarIcon, BookmarkIcon as BookmarkSolid } from '@heroicons/vue/24/solid';
import { BookmarkIcon as BookmarkOutline } from '@heroicons/vue/24/outline';
import { detailRoute } from '../../lib/format.js';
import { useWatchlist } from '../../lib/useWatchlist.js';

const props = defineProps({
    item: { type: Object, required: true },
    progress: { type: Number, default: null },
});

const { toggle } = useWatchlist();
const href = computed(() => detailRoute(props.item));
const rating = computed(() => Number(props.item.rating) || 0);
</script>

<template>
    <div class="group relative shrink-0">
        <div class="relative">
            <Link :href="href" class="block">
                <div
                    class="relative aspect-[2/3] overflow-hidden rounded-2xl bg-surface2 ring-1 ring-hair/10 transition-all duration-300 group-hover:-translate-y-1.5 group-hover:shadow-card group-hover:ring-accent/60"
                >
                    <img
                        v-if="item.poster"
                        :src="item.poster"
                        :alt="item.title"
                        loading="lazy"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.06]"
                    />
                    <div v-else class="grid h-full w-full place-items-center bg-aurora-soft text-center text-xs text-muted">
                        {{ item.title }}
                    </div>

                    <!-- top badges -->
                    <div class="absolute inset-x-0 top-0 flex items-start justify-between p-2">
                        <span
                            v-if="rating > 0"
                            class="flex items-center gap-1 rounded-full bg-black/60 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur"
                        >
                            <StarIcon class="h-3 w-3 text-yellow-400" />
                            {{ rating.toFixed(1) }}
                        </span>
                    </div>

                    <!-- hover overlay -->
                    <div
                        class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black via-black/55 to-transparent p-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                    >
                        <span
                            class="btn-play mb-2 h-11 w-11 self-start pointer-events-none"
                        >
                            <PlayIcon class="h-5 w-5" />
                        </span>
                        <p class="line-clamp-2 text-xs leading-snug text-white/80">{{ item.overview }}</p>
                    </div>

                    <div
                        v-if="progress != null && progress > 0"
                        class="absolute inset-x-0 bottom-0 h-1 bg-black/40"
                    >
                        <div class="h-full bg-accent transition-all" :style="{ width: `${Math.min(100, progress)}%` }" />
                    </div>
                </div>
            </Link>

            <button
                type="button"
                :aria-label="item.in_watchlist ? 'Remove from watchlist' : 'Add to watchlist'"
                class="absolute right-2 top-2 z-10 grid h-8 w-8 place-items-center rounded-full bg-black/55 text-white backdrop-blur transition hover:bg-accent"
                @click.stop="toggle(item)"
            >
                <BookmarkSolid v-if="item.in_watchlist" class="h-4 w-4 text-accent2" />
                <BookmarkOutline v-else class="h-4 w-4" />
            </button>
        </div>

        <div class="mt-2 px-0.5">
            <Link :href="href" class="line-clamp-1 text-sm font-semibold text-ink transition hover:text-accent">
                {{ item.title }}
            </Link>
            <p class="line-clamp-1 text-xs text-muted">
                <span v-if="item.year">{{ item.year }}</span>
                <span v-if="item.year && item.genres?.length"> · </span>
                <span v-if="item.genres?.length">{{ item.genres.slice(0, 2).join(', ') }}</span>
            </p>
        </div>
    </div>
</template>
