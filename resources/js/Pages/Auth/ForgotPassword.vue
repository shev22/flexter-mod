<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import AppButton from '../../Components/ui/AppButton.vue';
import { guestDataField } from '../../lib/guestMerge.js';

defineOptions({ layout: AuthLayout });

defineProps({
    status: { type: String, default: '' },
});

const form = useForm({ email: '' });

function submit() {
    form.post(route('password.email'));
}
</script>

<template>
    <Head title="Forgot password" />

    <h1 class="font-display text-3xl font-extrabold text-ink">Reset your password</h1>
    <p class="mt-2 text-sm text-muted">We will email you a link to choose a new password.</p>

    <p v-if="status" class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
        {{ status }}
    </p>

    <form class="mt-8 space-y-4" @submit.prevent="submit">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Email</label>
            <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
        </div>

        <AppButton type="submit" block :disabled="form.processing">Send reset link</AppButton>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        <Link :href="route('login')" class="font-semibold text-accent">Back to sign in</Link>
    </p>
</template>
