<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    HomeIcon,
    FilmIcon,
    TvIcon,
    UserGroupIcon,
    BookmarkIcon,
    Cog6ToothIcon,
    ChatBubbleLeftRightIcon,
    Squares2X2Icon,
    XMarkIcon,
    QueueListIcon,
    ChartBarIcon,
    BookOpenIcon,
    QuestionMarkCircleIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    open: { type: Boolean, default: false },
});
const emit = defineEmits(['close', 'mouseenter', 'mouseleave']);

const page = usePage();
const isAdmin = () => page.props.auth?.user?.is_admin === true;

const nav = [
    { label: 'Home', icon: HomeIcon, route: 'home', match: ['home'] },
    { label: 'Movies', icon: FilmIcon, route: 'movies', match: ['movies', 'movie.show'] },
    { label: 'Series', icon: TvIcon, route: 'tv', match: ['tv', 'tv.show'] },
    { label: 'Actors', icon: UserGroupIcon, route: 'actors', match: ['actors', 'actor.show'] },
];

const library = [
    { label: 'Watchlist', icon: BookmarkIcon, route: 'watchlist', match: ['watchlist'] },
    { label: 'Lists', icon: QueueListIcon, route: 'lists', match: ['lists', 'lists.show'] },
    { label: 'Diary', icon: BookOpenIcon, route: 'diary', match: ['diary'], auth: true },
    { label: 'Stats', icon: ChartBarIcon, route: 'stats', match: ['stats'], auth: true },
    { label: 'Settings', icon: Cog6ToothIcon, route: 'settings', match: ['settings'] },
];

function isActive(item) {
    return item.match.some((pattern) => route().current(pattern));
}
</script>

<template>
    <div>
        <!-- backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-200"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
                @click="emit('close')"
            />
        </Transition>

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-hair/10 bg-surface/85 px-4 py-6 backdrop-blur-2xl transition-transform duration-300"
            :class="open ? 'translate-x-0' : '-translate-x-full'"
            @mouseenter="$emit('mouseenter')"
            @mouseleave="$emit('mouseleave')"
        >
            <div class="mb-8 flex items-center justify-between px-2">
                <Link :href="route('home')" class="flex items-center gap-2" @click="emit('close')">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-aurora font-display text-lg font-black text-white shadow-glow">F</span>
                    <span class="font-display text-xl font-extrabold tracking-tight text-ink">FLEX<span class="text-gradient">TER</span></span>
                </Link>
                <button type="button" class="text-muted transition hover:text-ink" @click="emit('close')">
                    <XMarkIcon class="h-6 w-6" />
                </button>
            </div>

            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-muted">Browse</p>
            <nav class="space-y-1">
                <Link
                    v-for="item in nav"
                    :key="item.route"
                    :href="route(item.route)"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                    :class="isActive(item) ? 'bg-aurora-soft text-accent' : 'text-muted hover:bg-hair/5 hover:text-ink'"
                    @click="emit('close')"
                >
                    <component :is="item.icon" class="h-5 w-5 shrink-0" :class="isActive(item) ? 'text-accent' : ''" />
                    {{ item.label }}
                    <span v-if="isActive(item)" class="ml-auto h-1.5 w-1.5 rounded-full bg-accent" />
                </Link>
            </nav>

            <p class="mb-2 mt-7 px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-muted">My space</p>
            <nav class="space-y-1">
                <Link
                    v-for="item in library"
                    :key="item.route"
                    v-show="!item.auth || page.props.auth?.user"
                    :href="route(item.route)"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                    :class="isActive(item) ? 'bg-aurora-soft text-accent' : 'text-muted hover:bg-hair/5 hover:text-ink'"
                    @click="emit('close')"
                >
                    <component :is="item.icon" class="h-5 w-5 shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>

            <template v-if="isAdmin()">
                <p class="mb-2 mt-7 px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-muted">Admin</p>
                <nav class="space-y-1">
                    <a
                        href="/admin"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted transition hover:bg-hair/5 hover:text-ink"
                        @click="emit('close')"
                    >
                        <Squares2X2Icon class="h-5 w-5 shrink-0" />
                        Admin panel
                    </a>
                </nav>
            </template>

            <div class="mt-auto space-y-3">
                <Link
                    :href="route('help')"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                    :class="route().current('help') ? 'bg-aurora-soft text-accent' : 'text-muted hover:bg-hair/5 hover:text-ink'"
                    @click="emit('close')"
                >
                    <QuestionMarkCircleIcon class="h-5 w-5 shrink-0" />
                    Help
                </Link>
                <Link
                    :href="route('feedback')"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted transition hover:bg-hair/5 hover:text-ink"
                    @click="emit('close')"
                >
                    <ChatBubbleLeftRightIcon class="h-5 w-5 shrink-0" />
                    Feedback
                </Link>
                <div class="rounded-2xl bg-aurora-soft p-4">
                    <p class="font-display text-sm font-semibold text-ink">Discover more</p>
                    <p class="mt-1 text-xs text-muted">Thousands of titles updated daily from TMDB.</p>
                </div>
            </div>
        </aside>
    </div>
</template>
