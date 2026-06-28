<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowLeftIcon } from '@heroicons/vue/24/solid';
import MediaCard from '../../Components/ui/MediaCard.vue';
import EmptyState from '../../Components/ui/EmptyState.vue';
import ListIcon from '../../Components/ui/ListIcon.vue';

defineProps({
    list: { type: Object, required: true },
});
</script>

<template>
    <Head :title="list.title" />

    <div class="px-4 pb-16 lg:px-8">
        <Link :href="route('my.lists')" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-muted transition hover:text-accent">
            <ArrowLeftIcon class="h-4 w-4" /> My lists
        </Link>

        <div class="flex flex-col gap-4 py-6 sm:flex-row sm:items-start">
            <ListIcon :icon="list.icon" size="xl" />
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">
                    Your list · {{ list.visibility }}
                </p>
                <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">{{ list.title }}</h1>
                <p v-if="list.description" class="mt-3 max-w-2xl italic text-muted">{{ list.description }}</p>
                <Link
                    v-if="list.visibility === 'public'"
                    :href="route('lists.show', list.slug)"
                    class="mt-3 inline-block text-sm font-medium text-accent hover:underline"
                >
                    View public page
                </Link>
            </div>
        </div>

        <div v-if="list.items?.length" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            <MediaCard v-for="item in list.items" :key="`${item.type}-${item.id}`" :item="item" />
        </div>

        <EmptyState v-else title="This list is empty" description="Add titles from any movie or series page.">
            <template #action>
                <Link :href="route('movies')" class="text-sm font-semibold text-accent hover:underline">Browse movies</Link>
            </template>
        </EmptyState>
    </div>
</template>
