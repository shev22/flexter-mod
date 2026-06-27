<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import AppButton from '../../Components/ui/AppButton.vue';

defineOptions({ layout: AuthLayout });

defineProps({
    status: { type: String, default: '' },
});

const page = usePage();
const email = computed(() => page.props.auth?.user?.email ?? '');

const form = useForm({});

function resend() {
    form.post(route('verification.send'));
}
</script>

<template>
    <Head title="Verify email" />

    <h1 class="font-display text-3xl font-extrabold text-ink">Verify your email</h1>
    <p class="mt-2 text-sm text-muted">
        We sent a verification link to <span class="font-medium text-ink">{{ email }}</span>.
        Please check your inbox before streaming.
    </p>

    <p v-if="status === 'verification-link-sent'" class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
        A fresh verification link has been sent.
    </p>

    <div class="mt-8 space-y-4">
        <AppButton block :disabled="form.processing" @click="resend">Resend verification email</AppButton>
        <Link :href="route('home')" class="block text-center text-sm font-semibold text-accent">Browse while you wait</Link>
    </div>
</template>
