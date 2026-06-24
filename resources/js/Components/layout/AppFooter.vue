<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const page = usePage();
const site = computed(() => page.props.site ?? {});
const year = new Date().getFullYear();
</script>

<template>
    <footer class="mt-16 border-t border-hair/10 px-4 py-10 lg:px-12">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-display text-sm font-bold text-ink">{{ site.name || 'Flexter' }}</p>
                <p v-if="site.tagline" class="mt-1 max-w-md text-sm text-muted">{{ site.tagline }}</p>
                <p class="mt-3 text-xs text-muted/70">&copy; {{ year }} {{ site.name || 'Flexter' }}</p>
            </div>
            <nav class="flex flex-wrap gap-4 text-sm">
                <Link :href="route('help')" class="text-muted transition hover:text-accent">Help</Link>
                <Link :href="route('lists')" class="text-muted transition hover:text-accent">Curated lists</Link>
                <Link :href="route('feedback')" class="text-muted transition hover:text-accent">Send feedback</Link>
            </nav>
        </div>
    </footer>
</template>
