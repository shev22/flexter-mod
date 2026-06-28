<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MediaCard from '../../Components/ui/MediaCard.vue';
import EmptyState from '../../Components/ui/EmptyState.vue';
import { detailRoute } from '../../lib/format.js';

defineProps({
    entries: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Diary" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Your reviews</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">Watching diary</h1>
            <p class="mt-3 text-muted">Personal ratings and notes for everything you've watched.</p>
        </div>

        <div v-if="entries.length" class="space-y-4">
            <article
                v-for="entry in entries"
                :key="entry.id"
                class="rounded-3xl glass p-5 sm:flex sm:items-start sm:gap-5"
            >
                <Link
                    v-if="entry.media"
                    :href="detailRoute(entry.media)"
                    class="mb-4 block w-20 shrink-0 sm:mb-0"
                >
                    <div class="aspect-[2/3] overflow-hidden rounded-xl ring-1 ring-hair/10">
                        <img
                            v-if="entry.media.poster"
                            :src="entry.media.poster"
                            :alt="entry.media.title"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </Link>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="font-display text-lg font-bold text-ink">
                            <Link v-if="entry.media" :href="detailRoute(entry.media)" class="hover:text-accent">
                                {{ entry.media?.title || 'Untitled' }}
                            </Link>
                        </h2>
                        <span v-if="entry.rating" class="rounded-full bg-aurora-soft px-2.5 py-0.5 text-xs font-bold text-accent">
                            {{ entry.rating }}/10
                        </span>
                        <span v-if="entry.watched_on" class="text-xs text-muted">{{ entry.watched_on }}</span>
                    </div>
                    <p v-if="entry.body" class="mt-3 text-sm leading-relaxed text-muted">{{ entry.body }}</p>
                </div>
            </article>
        </div>

        <EmptyState
            v-else
            title="No diary entries yet"
            description="Rate and review titles from any movie or series detail page."
        />
    </div>
</template>
