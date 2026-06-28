<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppButton from '../ui/AppButton.vue';
import { useMediaReview } from '../../lib/useMediaReview.js';

const props = defineProps({
    media: { type: Object, required: true },
    review: { type: Object, default: null },
});

const { form, isAuthenticated, save, setRating } = useMediaReview(props.review, props.media);

const stars = computed(() => Array.from({ length: 10 }, (_, i) => i + 1));
</script>

<template>
    <section v-if="isAuthenticated" class="mt-12 rounded-3xl glass p-6">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Your diary</p>
                <h2 class="font-display text-xl font-bold text-ink">Rate &amp; review</h2>
            </div>
            <a :href="route('diary')" class="text-sm font-medium text-accent hover:underline">View diary</a>
        </div>

        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="star in stars"
                :key="star"
                type="button"
                class="grid h-9 w-9 place-items-center rounded-lg text-sm font-bold transition"
                :class="form.rating >= star ? 'bg-aurora text-white shadow-glow' : 'glass text-muted hover:text-ink'"
                @click="setRating(star)"
            >
                {{ star }}
            </button>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ink">Watched on</label>
                <input
                    v-model="form.watched_on"
                    type="date"
                    class="w-full rounded-xl glass px-4 py-3 text-sm text-ink"
                />
            </div>
            <div class="lg:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-ink">Notes</label>
                <textarea
                    v-model="form.body"
                    rows="3"
                    maxlength="2000"
                    placeholder="What did you think?"
                    class="w-full rounded-xl glass px-4 py-3 text-sm text-ink"
                />
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <AppButton type="button" :disabled="form.processing" @click="save">
                Save review
            </AppButton>
            <p v-if="form.errors.rating" class="text-xs text-rose-400">{{ form.errors.rating }}</p>
        </div>
    </section>
</template>
