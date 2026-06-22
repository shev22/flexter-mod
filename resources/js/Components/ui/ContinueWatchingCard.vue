<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PlayIcon } from '@heroicons/vue/24/solid';
import { detailRoute } from '../../lib/format.js';

const props = defineProps({
    item: { type: Object, required: true },
});

const href = computed(() => {
    let url = detailRoute(props.item);
    if (props.item.season != null && props.item.episode != null) {
        const sep = url.includes('?') ? '&' : '?';
        url += `${sep}season=${props.item.season}&episode=${props.item.episode}`;
    }
    return url;
});
</script>

<template>
    <Link :href="href" class="group relative block w-44 shrink-0">
        <div class="relative aspect-[2/3] overflow-hidden rounded-2xl bg-surface2 ring-1 ring-hair/10 transition group-hover:-translate-y-1 group-hover:ring-accent/60">
            <img
                v-if="item.poster"
                :src="item.poster"
                :alt="item.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div v-else class="grid h-full w-full place-items-center bg-aurora-soft p-3 text-center text-xs text-muted">
                {{ item.title }}
            </div>

            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent p-3 pt-10">
                <div class="mb-2 h-1 overflow-hidden rounded-full bg-white/20">
                    <div class="h-full rounded-full bg-accent" :style="{ width: `${item.progress}%` }" />
                </div>
                <p class="line-clamp-1 text-xs font-semibold text-white">{{ item.title }}</p>
                <p class="text-[10px] text-white/70">{{ item.progress }}% · {{ item.last_watched }}</p>
            </div>

            <span class="btn-play absolute left-2 top-2 h-8 w-8">
                <PlayIcon class="h-4 w-4" />
            </span>
        </div>
    </Link>
</template>
