<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PlusIcon } from '@heroicons/vue/24/solid';
import ListIcon from '../../Components/ui/ListIcon.vue';
import AppButton from '../../Components/ui/AppButton.vue';
import EmptyState from '../../Components/ui/EmptyState.vue';

defineProps({
    lists: { type: Array, default: () => [] },
});

const showForm = ref(false);

const form = useForm({
    title: '',
    description: '',
    visibility: 'private',
});

function submit() {
    form.post(route('my.lists.store'), {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
}
</script>

<template>
    <Head title="My lists" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4 py-10">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Your collections</p>
                <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">My lists</h1>
                <p class="mt-3 text-muted">Create and share your own movie and series collections.</p>
            </div>
            <AppButton type="button" @click="showForm = !showForm">
                <PlusIcon class="h-4 w-4" />
                New list
            </AppButton>
        </div>

        <form v-if="showForm" class="mb-8 rounded-3xl glass p-6" @submit.prevent="submit">
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Title</label>
                    <input v-model="form.title" type="text" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink" required />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-rose-400">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Visibility</label>
                    <select v-model="form.visibility" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink">
                        <option value="private">Private</option>
                        <option value="public">Public</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ink">Description</label>
                    <textarea v-model="form.description" rows="2" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink" />
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <AppButton type="submit" :disabled="form.processing">Create list</AppButton>
                <AppButton type="button" variant="ghost" @click="showForm = false">Cancel</AppButton>
            </div>
        </form>

        <div v-if="lists.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="list in lists"
                :key="list.id"
                :href="route('my.lists.show', list.slug)"
                class="rounded-2xl glass p-5 transition hover:ring-1 hover:ring-accent/40"
            >
                <div class="flex items-start gap-3">
                    <ListIcon :icon="list.icon" size="sm" />
                    <div class="min-w-0">
                        <h2 class="font-display text-lg font-bold text-ink">{{ list.title }}</h2>
                        <p class="mt-1 text-xs text-muted">
                            {{ list.item_count }} titles · {{ list.visibility }}
                        </p>
                    </div>
                </div>
            </Link>
        </div>

        <EmptyState v-else title="No lists yet" description="Create a list to start collecting titles." />
    </div>
</template>
