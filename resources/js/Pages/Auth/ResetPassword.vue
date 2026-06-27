<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import AppButton from '../../Components/ui/AppButton.vue';

defineOptions({ layout: AuthLayout });

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Choose new password" />

    <h1 class="font-display text-3xl font-extrabold text-ink">Choose a new password</h1>
    <p class="mt-2 text-sm text-muted">Enter your new password below.</p>

    <form class="mt-8 space-y-4" @submit.prevent="submit">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Email</label>
            <input v-model="form.email" type="email" autocomplete="email" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">New password</label>
            <input v-model="form.password" type="password" autocomplete="new-password" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
            <p v-if="form.errors.password" class="mt-1 text-xs text-rose-400">{{ form.errors.password }}</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ink">Confirm password</label>
            <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-xl glass px-4 py-3 text-sm text-ink focus:outline-none focus:ring-accent" />
        </div>

        <AppButton type="submit" block :disabled="form.processing">Update password</AppButton>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        <Link :href="route('login')" class="font-semibold text-accent">Back to sign in</Link>
    </p>
</template>
