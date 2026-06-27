<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import AppButton from '../../Components/ui/AppButton.vue';
import { guestDataField } from '../../lib/guestMerge.js';

defineOptions({ layout: AuthLayout });

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

function submit() {
    form
        .transform((data) => ({
            ...data,
            guest_data: guestDataField(),
        }))
        .post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head title="Create account" />

    <h1 class="font-display text-3xl font-extrabold text-ink">Create your account</h1>
    <p class="mt-2 text-sm text-muted">Join Flexter and start building your watchlist.</p>

    <form class="mt-8 space-y-4" @submit.prevent="submit">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Name</label>
            <input v-model="form.name" type="text" autocomplete="name" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Email</label>
            <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Password</label>
            <input v-model="form.password" type="password" autocomplete="new-password" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.password" class="mt-1 text-xs text-rose-400">{{ form.errors.password }}</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Confirm password</label>
            <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
        </div>

        <AppButton type="submit" block :disabled="form.processing">Create account</AppButton>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Already have an account?
        <Link :href="route('login')" class="font-semibold text-accent">Sign in</Link>
    </p>
</template>
