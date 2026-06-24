<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { TrashIcon } from '@heroicons/vue/24/outline';
import { detailRoute } from '../../lib/format.js';
import { useWatchHistory } from '../../lib/useWatchHistory.js';

const props = defineProps({
    items: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({ total: 0, completed: 0, in_progress: 0, hours: 0 }) },
});

const { remove, clearAll } = useWatchHistory();

const empty = computed(() => !props.items.length);

function href(item) {
    return detailRoute({ ...item, id: item.media_id });
}

function progressClass(progress) {
    if (progress >= 100) return 'bg-emerald-500';
    if (progress >= 60) return 'bg-accent';
    return 'bg-accent2';
}
</script>

<template>
    <div>
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-hair/10 bg-surface2/40 p-4">
                <p class="font-display text-2xl font-extrabold text-gradient">{{ stats.total }}</p>
                <p class="mt-1 text-xs text-muted">Total entries</p>
            </div>
            <div class="rounded-2xl border border-hair/10 bg-surface2/40 p-4">
                <p class="font-display text-2xl font-extrabold text-ink">{{ stats.completed }}</p>
                <p class="mt-1 text-xs text-muted">Completed</p>
            </div>
            <div class="rounded-2xl border border-hair/10 bg-surface2/40 p-4">
                <p class="font-display text-2xl font-extrabold text-ink">{{ stats.in_progress }}</p>
                <p class="mt-1 text-xs text-muted">In progress</p>
            </div>
            <div class="rounded-2xl border border-hair/10 bg-surface2/40 p-4">
                <p class="font-display text-2xl font-extrabold text-ink">{{ stats.hours }}h</p>
                <p class="mt-1 text-xs text-muted">Est. watch time</p>
            </div>
        </div>

        <div v-if="!empty" class="mb-4 flex justify-end">
            <button
                type="button"
                class="rounded-full border border-rose-500/30 px-4 py-2 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/10"
                @click="clearAll"
            >
                Clear all history
            </button>
        </div>

        <div v-if="empty" class="rounded-2xl border border-dashed border-hair/15 p-10 text-center">
            <p class="text-sm font-medium text-ink">No watch history yet</p>
            <p class="mt-1 text-xs text-muted">Open a movie or series detail page to start tracking your progress.</p>
        </div>

        <ul v-else class="space-y-3">
            <li
                v-for="item in items"
                :key="item.id"
                class="group flex items-center gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-3 transition hover:border-accent/30 hover:bg-hair/5"
            >
                <Link :href="href(item)" class="flex min-w-0 flex-1 items-center gap-4">
                    <div class="relative h-20 w-14 shrink-0 overflow-hidden rounded-xl bg-surface2 ring-1 ring-hair/10">
                        <img v-if="item.poster" :src="item.poster" :alt="item.title" class="h-full w-full object-cover" />
                        <div v-else class="grid h-full w-full place-items-center bg-aurora-soft text-xs font-bold text-accent">
                            {{ item.title?.[0] }}
                        </div>
                        <div class="absolute inset-x-0 bottom-0 h-1 bg-black/40">
                            <div class="h-full transition-all" :class="progressClass(item.progress)" :style="{ width: `${item.progress}%` }" />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="line-clamp-1 text-sm font-semibold text-ink group-hover:text-accent">{{ item.title }}</p>
                            <span class="rounded-full bg-hair/10 px-2 py-0.5 text-[10px] font-bold uppercase text-muted">
                                {{ item.type === 'tv' ? 'Series' : 'Movie' }}
                            </span>
                            <span
                                v-if="item.completed"
                                class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-500"
                            >
                                Done
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-muted">
                            <span v-if="item.year">{{ item.year }}</span>
                            <span v-if="item.year"> · </span>
                            {{ item.progress }}% watched
                            <span v-if="item.last_watched"> · {{ item.last_watched }}</span>
                        </p>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-hair/10">
                            <div class="h-full rounded-full transition-all" :class="progressClass(item.progress)" :style="{ width: `${item.progress}%` }" />
                        </div>
                    </div>
                </Link>

                <button
                    type="button"
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-muted transition hover:bg-rose-500/10 hover:text-rose-400"
                    aria-label="Remove from history"
                    @click="remove(item.id)"
                >
                    <TrashIcon class="h-4 w-4" />
                </button>
            </li>
        </ul>
    </div>
</template>
