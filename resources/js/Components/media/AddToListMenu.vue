<script setup>
import { onMounted, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { QueueListIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    media: { type: Object, required: true },
});

const page = usePage();
const open = ref(false);
const lists = ref([]);
const loading = ref(false);

async function loadLists() {
    if (!page.props.auth?.user) return;

    loading.value = true;

    try {
        const response = await fetch(
            `${route('my.lists.options')}?media_type=${props.media.type}&media_id=${props.media.id}`,
            { headers: { Accept: 'application/json' }, credentials: 'same-origin' },
        );
        const data = await response.json();
        lists.value = data.lists ?? [];
    } finally {
        loading.value = false;
    }
}

function toggleList(list) {
    const payload = {
        media_type: props.media.type,
        media_id: props.media.id,
    };

    if (list.contains) {
        router.delete(route('my.lists.items.destroy', list.slug), {
            data: payload,
            preserveScroll: true,
            onSuccess: () => loadLists(),
        });
    } else {
        router.post(route('my.lists.items.store', list.slug), payload, {
            preserveScroll: true,
            onSuccess: () => loadLists(),
        });
    }
}

onMounted(loadLists);
</script>

<template>
    <div v-if="page.props.auth?.user" class="relative">
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl glass px-4 py-2.5 text-sm font-semibold text-ink transition hover:ring-1 hover:ring-accent/40"
            @click="open = !open"
        >
            <QueueListIcon class="h-4 w-4" />
            Add to list
        </button>

        <div
            v-if="open"
            class="absolute right-0 z-20 mt-2 w-56 rounded-2xl glass-strong p-2 shadow-card"
        >
            <p v-if="loading" class="px-3 py-2 text-xs text-muted">Loading…</p>
            <template v-else-if="lists.length">
                <button
                    v-for="list in lists"
                    :key="list.id"
                    type="button"
                    class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm transition hover:bg-hair/10"
                    @click="toggleList(list)"
                >
                    <span class="truncate text-ink">{{ list.title }}</span>
                    <span class="text-xs" :class="list.contains ? 'text-accent2' : 'text-muted'">
                        {{ list.contains ? '✓' : '+' }}
                    </span>
                </button>
            </template>
            <p v-else class="px-3 py-2 text-xs text-muted">
                No lists yet.
                <a :href="route('my.lists')" class="font-semibold text-accent hover:underline">Create one</a>
            </p>
        </div>
    </div>
</template>
