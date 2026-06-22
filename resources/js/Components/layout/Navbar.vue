<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Bars3Icon, XMarkIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import { BookmarkIcon, Cog6ToothIcon, ArrowRightStartOnRectangleIcon, Squares2X2Icon, EyeSlashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    hero: { type: Boolean, default: false },
    sidebarOpen: { type: Boolean, default: false },
    maintenanceOffset: { type: Boolean, default: false },
});
const emit = defineEmits(['toggle-sidebar', 'open-search']);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.is_admin === true);
const site = computed(() => page.props.site ?? {});
const settings = computed(() => page.props.settings ?? {});
const menuOpen = ref(false);
const scrolled = ref(false);
const togglingSpoiler = ref(false);

// The bar floats over the content everywhere; it's see-through over a hero image
// until the user scrolls, and frosted otherwise.
const solid = computed(() => !props.hero || scrolled.value);

function onScroll() {
    scrolled.value = window.scrollY > 24;
}
onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));

const initials = computed(() =>
    (user.value?.name || '?')
        .split(' ')
        .slice(0, 2)
        .map((n) => n[0])
        .join('')
        .toUpperCase(),
);

function logout() {
    menuOpen.value = false;
    router.post(route('logout'));
}

function toggleSpoilerFree() {
    if (!user.value || togglingSpoiler.value) return;
    togglingSpoiler.value = true;
    const s = settings.value;

    router.patch(
        route('settings.update'),
        {
            theme: s.theme,
            accent: s.accent,
            autoplay_trailers: s.autoplay_trailers,
            reduce_motion: s.reduce_motion,
            subtitles: s.subtitles,
            maturity: s.maturity,
            density: s.density,
            high_contrast: s.high_contrast,
            language: s.language,
            email_notifications: s.email_notifications,
            spoiler_free: !s.spoiler_free,
        },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['settings', 'flash'],
            onFinish: () => {
                togglingSpoiler.value = false;
            },
        },
    );
}
</script>

<template>
    <header
        class="fixed inset-x-0 z-30 transition-all duration-300"
        :class="[
            solid ? 'border-b border-hair/10 bg-bg/70 backdrop-blur-xl' : 'bg-transparent',
            maintenanceOffset ? 'top-10' : 'top-0',
        ]"
    >
        <div class="flex h-16 items-center gap-3 px-4 lg:px-8">
            <button
                type="button"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl transition"
                :class="solid ? 'glass text-ink hover:bg-hair/10' : 'border border-white/15 bg-white/10 text-white backdrop-blur-md hover:bg-white/20'"
                :aria-label="sidebarOpen ? 'Hide menu' : 'Show menu'"
                @click="emit('toggle-sidebar')"
            >
                <XMarkIcon v-if="sidebarOpen" class="h-5 w-5" />
                <Bars3Icon v-else class="h-5 w-5" />
            </button>

            <Link
                :href="route('home')"
                class="hidden shrink-0 items-center gap-2 sm:flex"
                :class="solid ? 'text-ink' : 'text-white'"
            >
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-aurora font-display text-sm font-black text-white shadow-glow">F</span>
                <span class="font-display text-lg font-extrabold tracking-tight">{{ site.name || 'Flexter' }}</span>
            </Link>

            <button
                type="button"
                class="group flex flex-1 items-center gap-3 rounded-xl px-4 py-2.5 text-left text-sm transition lg:max-w-md"
                :class="solid ? 'glass text-muted hover:bg-hair/10' : 'border border-white/15 bg-white/10 text-white/80 backdrop-blur-md hover:bg-white/20'"
                @click="emit('open-search')"
            >
                <MagnifyingGlassIcon class="h-5 w-5" />
                <span class="flex-1">Search movies, shows, people…</span>
                <kbd class="hidden rounded-md border border-white/20 px-1.5 py-0.5 text-[10px] font-semibold sm:block">/</kbd>
            </button>

            <div class="ml-auto flex items-center gap-2">
                <template v-if="user">
                    <a
                        v-if="isAdmin"
                        href="/admin"
                        class="hidden rounded-full px-3.5 py-2 text-sm font-semibold transition sm:inline-flex sm:items-center sm:gap-1.5"
                        :class="solid ? 'glass text-ink hover:bg-hair/10' : 'border border-white/15 bg-white/10 text-white backdrop-blur-md hover:bg-white/20'"
                    >
                        <Squares2X2Icon class="h-4 w-4" />
                        Admin
                    </a>
                    <div class="relative">
                        <button
                            type="button"
                            class="grid h-10 w-10 place-items-center rounded-full bg-aurora font-display text-sm font-bold text-white shadow-glow"
                            @click="menuOpen = !menuOpen"
                        >
                            {{ initials }}
                        </button>
                        <Transition
                            enter-active-class="transition duration-150"
                            enter-from-class="opacity-0 -translate-y-1"
                            leave-active-class="transition duration-100"
                            leave-to-class="opacity-0"
                        >
                            <div v-if="menuOpen" class="absolute right-0 z-40 mt-2 w-56">
                                <div class="fixed inset-0 -z-10" @click="menuOpen = false" />
                                <div class="rounded-2xl glass-strong p-2 shadow-card">
                                    <div class="px-3 py-2">
                                        <p class="truncate text-sm font-semibold text-ink">{{ user.name }}</p>
                                        <p class="truncate text-xs text-muted">{{ user.email }}</p>
                                    </div>
                                    <div class="my-1 h-px bg-hair/10" />
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm text-ink transition hover:bg-hair/10"
                                        :disabled="togglingSpoiler"
                                        @click="toggleSpoilerFree"
                                    >
                                        <span class="flex items-center gap-2.5">
                                            <EyeSlashIcon class="h-4 w-4" /> Spoiler-free mode
                                        </span>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                                            :class="settings.spoiler_free ? 'bg-accent/20 text-accent' : 'bg-hair/10 text-muted'"
                                        >
                                            {{ settings.spoiler_free ? 'On' : 'Off' }}
                                        </span>
                                    </button>
                                    <div class="my-1 h-px bg-hair/10" />
                                    <Link :href="route('watchlist')" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink transition hover:bg-hair/10" @click="menuOpen = false">
                                        <BookmarkIcon class="h-4 w-4" /> Watchlist
                                    </Link>
                                    <Link :href="route('settings')" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink transition hover:bg-hair/10" @click="menuOpen = false">
                                        <Cog6ToothIcon class="h-4 w-4" /> Settings
                                    </Link>
                                    <a
                                        v-if="isAdmin"
                                        href="/admin"
                                        class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink transition hover:bg-hair/10"
                                        @click="menuOpen = false"
                                    >
                                        <Squares2X2Icon class="h-4 w-4" /> Admin panel
                                    </a>
                                    <button type="button" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-rose-400 transition hover:bg-rose-500/10" @click="logout">
                                        <ArrowRightStartOnRectangleIcon class="h-4 w-4" /> Sign out
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition hover:bg-white/10"
                        :class="solid ? 'text-ink' : 'text-white'"
                    >Sign in</Link>
                    <Link :href="route('register')" class="rounded-full bg-aurora px-4 py-2 text-sm font-semibold text-white shadow-glow transition hover:brightness-110">Join free</Link>
                </template>
            </div>
        </div>
    </header>
</template>
