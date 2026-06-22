<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppButton from '../../Components/ui/AppButton.vue';

const props = defineProps({
    defaults: {
        type: Object,
        default: () => ({ name: '', email: '' }),
    },
});

const form = useForm({
    name: props.defaults.name,
    email: props.defaults.email,
    subject: '',
    message: '',
});

function submit() {
    form.post(route('feedback.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('subject', 'message'),
    });
}
</script>

<template>
    <Head title="Feedback" />

    <div class="mx-auto max-w-2xl px-4 pb-20 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Get in touch</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">Send feedback</h1>
            <p class="mt-3 max-w-lg text-sm text-muted">
                Found a bug, have a feature idea, or just want to say hello? We'd love to hear from you.
            </p>
        </div>

        <form class="rounded-3xl glass p-6 sm:p-8" @submit.prevent="submit">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        class="w-full rounded-xl glass px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-accent"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ink">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        class="w-full rounded-xl glass px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-accent"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
                </div>
            </div>

            <div class="mt-5">
                <label class="mb-1.5 block text-sm font-medium text-ink">Subject</label>
                <input
                    v-model="form.subject"
                    type="text"
                    class="w-full rounded-xl glass px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-accent"
                />
                <p v-if="form.errors.subject" class="mt-1 text-xs text-rose-400">{{ form.errors.subject }}</p>
            </div>

            <div class="mt-5">
                <label class="mb-1.5 block text-sm font-medium text-ink">Message</label>
                <textarea
                    v-model="form.message"
                    rows="6"
                    class="w-full resize-y rounded-xl glass px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-accent"
                    placeholder="Tell us what's on your mind…"
                />
                <p v-if="form.errors.message" class="mt-1 text-xs text-rose-400">{{ form.errors.message }}</p>
            </div>

            <AppButton type="submit" class="mt-7" :disabled="form.processing">
                Send message
            </AppButton>
        </form>
    </div>
</template>
