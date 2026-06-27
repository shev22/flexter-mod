<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import AppButton from '../../Components/ui/AppButton.vue';
import { guestDataField } from '../../lib/guestMerge.js';

defineOptions({ layout: AuthLayout });

const status = computed(() => usePage().props.flash?.status ?? '');

const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form
        .transform((data) => ({
            ...data,
            guest_data: guestDataField(),
        }))
        .post(route('login'), { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head title="Sign in" />

    <h1 class="font-display text-3xl font-extrabold text-ink">Welcome back</h1>
    <p class="mt-2 text-sm text-muted">Sign in to continue to your watchlist.</p>

    <p v-if="status" class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
        {{ status }}
    </p>

    <form class="mt-8 space-y-4" @submit.prevent="submit">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Email</label>
            <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
        </div>
        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label class="text-sm font-medium text-ink">Password</label>
                <Link :href="route('password.request')" class="text-xs font-semibold text-accent">Forgot password?</Link>
            </div>
            <input v-model="form.password" type="password" autocomplete="current-password" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.password" class="mt-1 text-xs text-rose-400">{{ form.errors.password }}</p>
        </div>
        <label class="flex items-center gap-2 text-sm text-muted">
            <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-hair/20 bg-transparent text-accent focus:ring-accent" />
            Remember me
        </label>

        <AppButton type="submit" block :disabled="form.processing">Sign in</AppButton>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        New to Flexter?
        <Link :href="route('register')" class="font-semibold text-accent">Create an account</Link>
    </p>
</template>
