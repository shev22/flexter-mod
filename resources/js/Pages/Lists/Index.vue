<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { QueueListIcon } from '@heroicons/vue/24/solid';
import EmptyState from '../../Components/ui/EmptyState.vue';
import AppButton from '../../Components/ui/AppButton.vue';
import ListIcon from '../../Components/ui/ListIcon.vue';

defineProps({
    lists: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Curated lists" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Collections</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">Curated lists</h1>
            <p class="mt-3 text-muted">Hand-picked collections to help you discover your next favourite.</p>
        </div>

        <div v-if="lists.length" class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            <Link
                v-for="list in lists"
                :key="list.slug"
                :href="route('lists.show', list.slug)"
                class="group rounded-2xl glass p-4 transition hover:ring-1 hover:ring-accent/40"
            >
                <ListIcon :icon="list.icon" size="sm" />
                <h2 class="mt-3 font-display text-base font-bold text-ink group-hover:text-accent sm:text-lg">{{ list.title }}</h2>
                <p v-if="list.description" class="mt-1.5 line-clamp-2 text-xs italic text-muted sm:text-sm">{{ list.description }}</p>
                <p class="mt-3 text-[10px] font-semibold uppercase tracking-wide text-accent sm:text-xs">{{ list.item_count }} titles</p>
            </Link>
        </div>

        <EmptyState v-else title="No lists yet" description="Check back soon for curated collections.">
            <template #icon><QueueListIcon class="h-7 w-7" /></template>
            <template #action><AppButton :href="route('home')">Back to home</AppButton></template>
        </EmptyState>
    </div>
</template>
