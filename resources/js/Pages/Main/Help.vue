<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    HomeIcon,
    FilmIcon,
    MagnifyingGlassIcon,
    BookmarkIcon,
    SparklesIcon,
    QueueListIcon,
    UserGroupIcon,
    ChartBarIcon,
    Cog6ToothIcon,
    ChatBubbleLeftRightIcon,
    CommandLineIcon,
} from '@heroicons/vue/24/outline';

const sections = [
    {
        icon: HomeIcon,
        title: 'Home',
        items: [
            'The hero carousel highlights trending movies and series — hover a slide to preview its trailer (if enabled in Settings).',
            'Continue watching picks up where you left off (requires an account).',
            'Recommended for you uses your favorite genres or watch history to suggest titles.',
            'From actors you follow shows fresh titles featuring actors you have favorited.',
            'Featured lists are curated collections — browse themed picks like horror or action.',
            'Movie and series rails below surface popular, top rated, and new releases.',
        ],
    },
    {
        icon: FilmIcon,
        title: 'Movies & series',
        items: [
            'Browse the full catalogue from Movies or Series in the sidebar.',
            'Open any title for overview, cast, rating, genres, and related titles.',
            'Hover the backdrop on a detail page to play a muted trailer preview.',
            'Use Play trailer for the full-screen player — progress can update your watch history.',
        ],
    },
    {
        icon: MagnifyingGlassIcon,
        title: 'Search',
        items: [
            'Press / or use the search icon in the navbar to find movies, series, and people.',
            'Results update as you type. Open a result to view its detail page.',
        ],
    },
    {
        icon: BookmarkIcon,
        title: 'Watchlist & tonight queue',
        items: [
            'Add titles to your watchlist from any card or detail page with the + button.',
            'Watchlist supports movies, series, and bulk remove when signed in.',
            'Add to tonight queues titles you plan to watch later — managed on the watchlist page.',
            'Empty watchlist? We suggest popular titles to get you started.',
        ],
    },
    {
        icon: SparklesIcon,
        title: 'Watch progress & history',
        items: [
            'Mark watched or let trailer progress update your percentage on detail pages.',
            'For series, pick season and episode before marking progress.',
            'Continue watching on home reflects your latest activity.',
            'Clear or remove individual entries from Settings → Watch history.',
        ],
    },
    {
        icon: Cog6ToothIcon,
        title: 'Settings & personalization',
        items: [
            'Theme, accent color, and density adjust how Flexter looks.',
            'Favorite genres power recommendations — pick up to five in Settings.',
            'Autoplay trailers controls hover previews on home and detail pages.',
            'Spoiler-free mode blurs overviews until you choose to reveal them.',
            'Reduce motion disables hero autoplay and carousel animations.',
        ],
    },
    {
        icon: QueueListIcon,
        title: 'Curated lists',
        items: [
            'Public collections grouped by genre or mood — open Lists in the sidebar.',
            'Each list shows posters and a full grid on its detail page.',
        ],
    },
    {
        icon: UserGroupIcon,
        title: 'Actors',
        items: [
            'Browse actors and open a profile to see their filmography.',
            'Follow actors while signed in — their new work appears in your home feed.',
        ],
    },
    {
        icon: ChartBarIcon,
        title: 'Stats',
        items: [
            'Signed-in members can view watching stats: time spent, genres, and activity summaries.',
        ],
    },
    {
        icon: CommandLineIcon,
        title: 'Keyboard shortcuts',
        items: [
            '? — open shortcut help',
            '/ — focus search',
            'g then h — Home',
            'g then m — Movies',
            'g then t — Series',
            'g then w — Watchlist',
            'g then s — Settings',
            'Esc — close modals',
        ],
    },
    {
        icon: ChatBubbleLeftRightIcon,
        title: 'Feedback',
        items: [
            'Use Feedback in the sidebar to report bugs or suggest features.',
            'We read every message — no account required, but signing in pre-fills your details.',
        ],
    },
];
</script>

<template>
    <Head title="Help" />

    <div class="mx-auto max-w-3xl px-4 pb-20 lg:px-8">
        <div class="py-10">
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Guide</p>
            <h1 class="font-display text-4xl font-extrabold text-ink sm:text-5xl">How Flexter works</h1>
            <p class="mt-3 max-w-xl text-muted">
                Everything you can do in the app — from discovering titles to tracking what you watch.
            </p>
        </div>

        <div class="space-y-4">
            <section
                v-for="section in sections"
                :key="section.title"
                class="rounded-3xl glass p-5 sm:p-6"
            >
                <div class="mb-4 flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-aurora-soft text-accent">
                        <component :is="section.icon" class="h-5 w-5" />
                    </span>
                    <h2 class="font-display text-lg font-bold text-ink">{{ section.title }}</h2>
                </div>
                <ul class="space-y-2.5 pl-1">
                    <li
                        v-for="(item, idx) in section.items"
                        :key="idx"
                        class="flex gap-2.5 text-sm leading-relaxed text-muted"
                    >
                        <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-accent" aria-hidden="true" />
                        <span>{{ item }}</span>
                    </li>
                </ul>
            </section>
        </div>

        <div class="mt-10 rounded-3xl bg-aurora-soft p-6 text-center">
            <p class="font-display text-base font-semibold text-ink">Still stuck?</p>
            <p class="mt-1 text-sm text-muted">Send us a note and we will get back to you.</p>
            <Link
                :href="route('feedback')"
                class="mt-4 inline-flex rounded-full bg-aurora px-6 py-2.5 text-sm font-semibold text-white shadow-glow transition hover:opacity-90"
            >
                Contact support
            </Link>
        </div>
    </div>
</template>
