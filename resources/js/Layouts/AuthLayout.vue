<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>

<template>
    <div class="relative grid min-h-screen bg-bg text-ink lg:grid-cols-2">
        <!-- cinematic panel -->
        <div class="relative hidden overflow-hidden lg:block">
            <div class="absolute inset-0 bg-aurora opacity-90" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.25),transparent_55%)]" />
            <div class="relative flex h-full flex-col justify-between p-12">
                <Link :href="route('home')" class="flex items-center gap-2 text-white">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/20 font-display text-xl font-black backdrop-blur">F</span>
                    <span class="font-display text-2xl font-extrabold tracking-tight">FLEXTER</span>
                </Link>
                <div>
                    <h1 class="font-display text-4xl font-extrabold leading-tight text-white">
                        Every story,<br />beautifully in one place.
                    </h1>
                    <p class="mt-4 max-w-md text-white/80">
                        Track movies and series, follow your favourite actors, and build a watchlist that follows you everywhere.
                    </p>
                </div>
                <p class="text-sm text-white/60">Powered by TMDB · Crafted for cinephiles</p>
            </div>
        </div>

        <!-- form panel -->
        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">
                <slot />
            </div>
        </div>
    </div>
</template>
