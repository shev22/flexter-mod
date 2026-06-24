<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    currentPage: { type: Number, required: true },
    lastPage: { type: Number, required: true },
});

const pages = computed(() => {
    const total = props.lastPage;
    const current = props.currentPage;
    const span = [];
    const start = Math.max(1, current - 2);
    const end = Math.min(total, current + 2);
    for (let i = start; i <= end; i++) span.push(i);
    return span;
});

function go(page) {
    if (page < 1 || page > props.lastPage || page === props.currentPage) return;
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    router.visit(url.pathname + url.search, { preserveScroll: false });
}
</script>

<template>
    <nav v-if="lastPage > 1" class="mt-10 flex items-center justify-center gap-1.5">
        <button
            type="button"
            class="grid h-10 w-10 place-items-center rounded-xl glass text-ink transition hover:text-accent disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="go(currentPage - 1)"
        >
            <ChevronLeftIcon class="h-5 w-5" />
        </button>

        <button
            v-if="pages[0] > 1"
            type="button"
            class="h-10 w-10 rounded-xl glass text-sm font-semibold text-muted transition hover:text-accent"
            @click="go(1)"
        >
            1
        </button>
        <span v-if="pages[0] > 2" class="px-1 text-muted">…</span>

        <button
            v-for="page in pages"
            :key="page"
            type="button"
            class="h-10 w-10 rounded-xl text-sm font-semibold transition"
            :class="page === currentPage ? 'bg-aurora text-white shadow-glow' : 'glass text-muted hover:text-accent'"
            @click="go(page)"
        >
            {{ page }}
        </button>

        <span v-if="pages[pages.length - 1] < lastPage - 1" class="px-1 text-muted">…</span>
        <button
            v-if="pages[pages.length - 1] < lastPage"
            type="button"
            class="h-10 w-10 rounded-xl glass text-sm font-semibold text-muted transition hover:text-accent"
            @click="go(lastPage)"
        >
            {{ lastPage }}
        </button>

        <button
            type="button"
            class="grid h-10 w-10 place-items-center rounded-xl glass text-ink transition hover:text-accent disabled:opacity-40"
            :disabled="currentPage === lastPage"
            @click="go(currentPage + 1)"
        >
            <ChevronRightIcon class="h-5 w-5" />
        </button>
    </nav>
</template>
