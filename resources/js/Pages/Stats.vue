<script setup>
import { Head } from '@inertiajs/vue3';
import {
    ChartBarIcon,
    TrophyIcon,
    ClockIcon,
    FilmIcon,
    SparklesIcon,
} from '@heroicons/vue/24/solid';

defineProps({
    profile: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Your stats" />

    <div class="px-4 pb-16 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Your journey</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">Viewing stats</h1>
            <p class="mt-3 text-muted">A snapshot of what you have watched on Flexter.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl glass p-5">
                <FilmIcon class="h-6 w-6 text-accent" />
                <p class="mt-3 font-display text-3xl font-extrabold text-ink">{{ profile.totals.entries }}</p>
                <p class="text-sm text-muted">Titles tracked</p>
            </div>
            <div class="rounded-3xl glass p-5">
                <SparklesIcon class="h-6 w-6 text-accent2" />
                <p class="mt-3 font-display text-3xl font-extrabold text-ink">{{ profile.totals.completed }}</p>
                <p class="text-sm text-muted">Completed</p>
            </div>
            <div class="rounded-3xl glass p-5">
                <ChartBarIcon class="h-6 w-6 text-accent" />
                <p class="mt-3 font-display text-3xl font-extrabold text-ink">{{ profile.totals.in_progress }}</p>
                <p class="text-sm text-muted">In progress</p>
            </div>
            <div class="rounded-3xl glass p-5">
                <ClockIcon class="h-6 w-6 text-accent2" />
                <p class="mt-3 font-display text-3xl font-extrabold text-ink">{{ profile.totals.hours }}</p>
                <p class="text-sm text-muted">Hours watched</p>
            </div>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-2">
            <section class="rounded-3xl glass p-6">
                <h2 class="font-display text-xl font-bold text-ink">Top genres</h2>
                <ul v-if="profile.top_genres.length" class="mt-5 space-y-3">
                    <li v-for="genre in profile.top_genres" :key="genre.label" class="flex items-center gap-3">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-hair/10">
                            <div
                                class="h-full rounded-full bg-aurora"
                                :style="{ width: `${Math.min(100, (genre.count / profile.top_genres[0].count) * 100)}%` }"
                            />
                        </div>
                        <span class="w-28 shrink-0 text-sm font-semibold text-ink">{{ genre.label }}</span>
                        <span class="text-xs text-muted">{{ genre.count }}</span>
                    </li>
                </ul>
                <p v-else class="mt-4 text-sm text-muted">Watch more titles to unlock genre insights.</p>
            </section>

            <section class="rounded-3xl glass p-6">
                <h2 class="font-display text-xl font-bold text-ink">Recent activity</h2>
                <ul v-if="profile.recent_months.length" class="mt-5 space-y-3">
                    <li
                        v-for="month in profile.recent_months"
                        :key="month.month"
                        class="flex items-center justify-between rounded-xl bg-surface2/50 px-4 py-3"
                    >
                        <span class="text-sm font-medium text-ink">{{ month.month }}</span>
                        <span class="rounded-full bg-aurora-soft px-2.5 py-0.5 text-xs font-bold text-accent">{{ month.count }} titles</span>
                    </li>
                </ul>
                <p v-else class="mt-4 text-sm text-muted">No recent activity yet.</p>
            </section>
        </div>

        <section v-if="profile.badges.length" class="mt-12">
            <div class="mb-6 flex items-center gap-3">
                <TrophyIcon class="h-6 w-6 text-yellow-400" />
                <h2 class="font-display text-xl font-bold text-ink">Badges earned</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="badge in profile.badges"
                    :key="badge.key"
                    class="rounded-3xl bg-aurora-soft p-5 ring-1 ring-accent/20"
                >
                    <p class="font-display text-lg font-bold text-ink">{{ badge.label }}</p>
                    <p class="mt-1 text-sm text-muted">{{ badge.description }}</p>
                </div>
            </div>
        </section>
    </div>
</template>
