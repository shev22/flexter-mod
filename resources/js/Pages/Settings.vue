<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    CheckIcon,
    Cog6ToothIcon,
    PaintBrushIcon,
    PlayCircleIcon,
    ClockIcon,
    UserCircleIcon,
    ShieldCheckIcon,
    SparklesIcon,
    CreditCardIcon,
} from '@heroicons/vue/24/solid';
import Toggle from '../Components/ui/Toggle.vue';
import SelectMenu from '../Components/ui/SelectMenu.vue';
import MultiSelectMenu from '../Components/ui/MultiSelectMenu.vue';
import AppButton from '../Components/ui/AppButton.vue';
import WatchHistoryList from '../Components/settings/WatchHistoryList.vue';
import { applyAppearance } from '../lib/appearance.js';
import { useBilling } from '../lib/useBilling.js';

const props = defineProps({
    settings: { type: Object, required: true },
    stats: { type: Object, required: true },
    historyStats: { type: Object, default: () => ({ total: 0, completed: 0, in_progress: 0, hours: 0 }) },
    memberSince: { type: String, default: '' },
    billing: { type: Object, default: () => ({}) },
});

const history = ref([]);
const historyStatsLocal = ref({ ...props.historyStats });
const historyLoading = ref(false);
const historyLoaded = ref(false);

const authUser = usePage().props.auth?.user ?? {};
const { paymentsEnabled, isSubscribed, formattedPrice, plan } = useBilling();
const genreOptions = computed(() =>
    (usePage().props.genres ?? []).map((g) => ({ value: g.id, label: g.name })),
);

const tabs = computed(() => {
    const items = [
        { id: 'overview', label: 'Overview', icon: SparklesIcon },
    ];

    if (paymentsEnabled.value) {
        items.push({ id: 'billing', label: 'Subscription', icon: CreditCardIcon });
    }

    items.push(
        { id: 'appearance', label: 'Appearance', icon: PaintBrushIcon },
        { id: 'playback', label: 'Playback', icon: PlayCircleIcon },
        { id: 'history', label: 'Watch history', icon: ClockIcon },
        { id: 'account', label: 'Account', icon: UserCircleIcon },
        { id: 'privacy', label: 'Privacy', icon: ShieldCheckIcon },
    );

    return items;
});

const activeTab = ref('overview');

const profile = useForm({
    name: authUser.name ?? '',
    email: authUser.email ?? '',
    password: '',
    password_confirmation: '',
});

const prefs = useForm({
    theme: props.settings.theme,
    accent: props.settings.accent,
    autoplay_trailers: props.settings.autoplay_trailers,
    reduce_motion: props.settings.reduce_motion,
    subtitles: props.settings.subtitles,
    allow_adult: props.settings.allow_adult ?? false,
    density: props.settings.density,
    high_contrast: props.settings.high_contrast,
    language: props.settings.language,
    email_notifications: props.settings.email_notifications,
    spoiler_free: props.settings.spoiler_free,
    favorite_genre_ids: props.settings.favorite_genre_ids ?? [],
});

const themes = [
    { value: 'dark', label: 'Dark', hint: 'Cinematic default', swatch: '#08080e' },
    { value: 'light', label: 'Light', hint: 'Bright & clean', swatch: '#f5f5f8' },
    { value: 'cream', label: 'Cream', hint: 'Soft milky white', swatch: '#fcf9f1' },
    { value: 'system', label: 'System', hint: 'Follow OS', swatch: 'linear-gradient(135deg, #08080e 50%, #f5f5f8 50%)' },
];

const accents = computed(() => props.settings.accents ?? []);

const densityOptions = [
    { value: 'compact', label: 'Compact' },
    { value: 'comfortable', label: 'Comfortable' },
    { value: 'spacious', label: 'Spacious' },
];

const languageOptions = [
    { value: 'en', label: 'English' },
    { value: 'es', label: 'Español' },
    { value: 'fr', label: 'Français' },
    { value: 'de', label: 'Deutsch' },
];

const statCards = computed(() => [
    { label: 'Watchlist', value: props.stats.watchlist, hint: 'Saved titles' },
    { label: 'Watched', value: historyStatsLocal.value.completed, hint: 'Completed' },
    { label: 'In progress', value: historyStatsLocal.value.in_progress, hint: 'Continue watching' },
    { label: 'Est. hours', value: `${historyStatsLocal.value.hours}h`, hint: 'Time tracked' },
]);

const initials = computed(() =>
    (authUser.name || '?')
        .split(' ')
        .slice(0, 2)
        .map((n) => n[0])
        .join('')
        .toUpperCase(),
);

watch(
    () => [
        prefs.theme,
        prefs.accent,
        prefs.reduce_motion,
        prefs.density,
        prefs.high_contrast,
    ],
    () =>
        applyAppearance({
            theme: prefs.theme,
            accent: prefs.accent,
            reduce_motion: prefs.reduce_motion,
            density: prefs.density,
            high_contrast: prefs.high_contrast,
        }),
);

async function loadHistory() {
    if (historyLoaded.value || historyLoading.value) return;
    historyLoading.value = true;
    try {
        const { data } = await window.axios.get(route('settings.history'));
        history.value = data.history ?? [];
        historyStatsLocal.value = data.historyStats ?? historyStatsLocal.value;
        historyLoaded.value = true;
    } finally {
        historyLoading.value = false;
    }
}

watch(activeTab, (tab) => {
    if (tab === 'history') {
        loadHistory();
    }
});

function savePrefs() {
    const previousAllowAdult = props.settings.allow_adult;

    prefs.patch(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            if (prefs.allow_adult !== previousAllowAdult) {
                router.reload({ preserveScroll: true });
            }
        },
    });
}

function saveProfile() {
    profile.patch(route('settings.profile'), {
        preserveScroll: true,
        onSuccess: () => profile.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Settings" />

    <div class="mx-auto max-w-6xl px-4 pb-20 lg:px-8">
        <!-- hero header -->
        <div class="relative overflow-hidden rounded-3xl glass-strong p-8 sm:p-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-accent/20 blur-3xl" />
            <div class="pointer-events-none absolute -bottom-20 -left-10 h-56 w-56 rounded-full bg-accent2/15 blur-3xl" />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-aurora font-display text-xl font-bold text-white shadow-glow">
                    {{ initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent">Your space</p>
                    <h1 class="font-display text-3xl font-extrabold text-ink sm:text-4xl">Settings &amp; preferences</h1>
                    <p class="mt-2 text-sm text-muted">
                        {{ authUser.email }}
                        <span v-if="memberSince"> · Member since {{ memberSince }}</span>
                    </p>
                </div>
                <AppButton :href="route('watchlist')" class="shrink-0">Open watchlist</AppButton>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-8 lg:flex-row">
            <!-- sidebar tabs -->
            <nav class="lg:w-56 lg:shrink-0">
                <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible lg:pb-0">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        class="flex shrink-0 items-center gap-2.5 rounded-2xl px-4 py-3 text-left text-sm font-semibold transition"
                        :class="activeTab === tab.id
                            ? 'bg-aurora text-white shadow-glow'
                            : 'glass text-muted hover:text-ink'"
                        @click="activeTab = tab.id"
                    >
                        <component :is="tab.icon" class="h-5 w-5 shrink-0" />
                        {{ tab.label }}
                    </button>
                </div>
            </nav>

            <!-- content -->
            <div class="min-w-0 flex-1">
                <!-- Overview -->
                <section v-if="activeTab === 'overview'" class="space-y-6">
                    <div class="rounded-3xl glass p-6">
                        <h2 class="font-display text-xl font-bold text-ink">At a glance</h2>
                        <p class="mt-1 text-sm text-muted">Your activity across Flexter.</p>
                        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                            <div v-for="card in statCards" :key="card.label" class="rounded-2xl border border-hair/10 bg-surface2/40 p-4">
                                <p class="font-display text-2xl font-extrabold text-gradient">{{ card.value }}</p>
                                <p class="mt-1 text-sm font-medium text-ink">{{ card.label }}</p>
                                <p class="text-xs text-muted">{{ card.hint }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl glass p-6">
                        <h2 class="font-display text-xl font-bold text-ink">Favorite genres</h2>
                        <p class="mt-1 text-sm text-muted">
                            Pick up to 5 genres you love. These power the <strong class="font-medium text-ink">Recommended for you</strong> rail on the home page.
                            If none are selected, recommendations fall back to your watch history.
                        </p>
                        <div class="mt-5 max-w-md">
                            <MultiSelectMenu
                                v-model="prefs.favorite_genre_ids"
                                :options="genreOptions"
                                placeholder="Choose genres…"
                            />
                            <p v-if="prefs.errors.favorite_genre_ids" class="mt-1 text-xs text-rose-400">{{ prefs.errors.favorite_genre_ids }}</p>
                        </div>
                        <AppButton class="mt-5" :disabled="prefs.processing" @click="savePrefs">Save genres</AppButton>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <button type="button" class="rounded-2xl border border-hair/10 bg-surface2/30 p-5 text-left transition hover:border-accent/30" @click="activeTab = 'appearance'">
                            <PaintBrushIcon class="h-6 w-6 text-accent" />
                            <p class="mt-3 font-semibold text-ink">Personalise appearance</p>
                            <p class="mt-1 text-sm text-muted">18 accent palettes, density, high contrast.</p>
                        </button>
                        <button type="button" class="rounded-2xl border border-hair/10 bg-surface2/30 p-5 text-left transition hover:border-accent/30" @click="activeTab = 'history'">
                            <ClockIcon class="h-6 w-6 text-accent" />
                            <p class="mt-3 font-semibold text-ink">Watch history</p>
                            <p class="mt-1 text-sm text-muted">{{ historyStatsLocal.total }} entries tracked with progress.</p>
                        </button>
                    </div>

                    <div class="rounded-3xl glass p-6">
                        <div class="flex items-center gap-2 text-sm text-muted">
                            <Cog6ToothIcon class="h-5 w-5 text-accent" />
                            Current theme: <span class="font-semibold capitalize text-ink">{{ prefs.theme }}</span>
                            · Accent: <span class="font-semibold capitalize text-ink">{{ prefs.accent }}</span>
                        </div>
                    </div>
                </section>

                <!-- Appearance -->
                <section v-else-if="activeTab === 'appearance'" class="rounded-3xl glass p-6 sm:p-8">
                    <h2 class="font-display text-xl font-bold text-ink">Appearance</h2>
                    <p class="mt-1 text-sm text-muted">Changes preview instantly. Save when you're happy.</p>

                    <div class="mt-8">
                        <p class="mb-3 text-sm font-medium text-ink">Theme mode</p>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <button
                                v-for="t in themes"
                                :key="t.value"
                                type="button"
                                class="rounded-2xl border p-4 text-left transition"
                                :class="prefs.theme === t.value
                                    ? 'border-accent/50 bg-aurora-soft ring-1 ring-accent/30'
                                    : 'border-hair/10 bg-surface2/30 hover:border-hair/20'"
                                @click="prefs.theme = t.value"
                            >
                                <span
                                    class="mb-3 block h-8 w-full rounded-lg ring-1 ring-hair/10"
                                    :style="{ background: t.swatch }"
                                />
                                <p class="font-semibold text-ink">{{ t.label }}</p>
                                <p class="mt-1 text-xs text-muted">{{ t.hint }}</p>
                            </button>
                        </div>
                    </div>

                    <div class="mt-8">
                        <p class="mb-3 text-sm font-medium text-ink">Accent palette</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="a in accents"
                                :key="a.value"
                                type="button"
                                class="group relative h-8 w-8 shrink-0 rounded-md ring-2 ring-offset-1 ring-offset-bg transition"
                                :class="prefs.accent === a.value ? 'ring-white' : 'ring-transparent hover:ring-hair/25'"
                                :style="a.solid
                                    ? { backgroundColor: a.from }
                                    : { background: `linear-gradient(135deg, ${a.from}, ${a.to})` }"
                                :title="a.label"
                                @click="prefs.accent = a.value"
                            >
                                <CheckIcon v-if="prefs.accent === a.value" class="absolute inset-0 m-auto h-3.5 w-3.5 text-white drop-shadow" />
                                <span class="sr-only">{{ a.label }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div>
                            <p class="mb-2 text-sm font-medium text-ink">Interface density</p>
                            <SelectMenu v-model="prefs.density" :options="densityOptions" />
                        </div>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                            <span>
                                <span class="block text-sm font-medium text-ink">High contrast</span>
                                <span class="text-xs text-muted">Sharper text and borders.</span>
                            </span>
                            <Toggle v-model="prefs.high_contrast" />
                        </label>
                    </div>

                    <!-- live preview -->
                    <div class="mt-8 overflow-hidden rounded-2xl border border-hair/10 bg-surface2/40 p-5">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted">Live preview</p>
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-12 rounded-lg bg-aurora shadow-glow" />
                            <div>
                                <p class="font-display text-lg font-bold text-ink">Sample title</p>
                                <p class="text-sm text-muted">Jul, 2024 · <span class="text-accent">Accent text</span></p>
                                <span class="mt-2 inline-block rounded-full bg-aurora px-3 py-1 text-xs font-bold text-white">Button</span>
                            </div>
                        </div>
                    </div>

                    <AppButton class="mt-8" :disabled="prefs.processing" @click="savePrefs">Save appearance</AppButton>
                </section>

                <!-- Playback -->
                <section v-else-if="activeTab === 'playback'" class="rounded-3xl glass p-6 sm:p-8">
                    <h2 class="font-display text-xl font-bold text-ink">Playback &amp; content</h2>
                    <p class="mt-1 text-sm text-muted">Control trailers, language, and content filters.</p>

                    <div class="mt-8 space-y-5">
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                            <span><span class="block text-sm font-medium text-ink">Autoplay trailers</span><span class="text-xs text-muted">Background trailers on hero and detail pages.</span></span>
                            <Toggle v-model="prefs.autoplay_trailers" />
                        </label>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                            <span><span class="block text-sm font-medium text-ink">Reduce motion</span><span class="text-xs text-muted">Minimise animations and parallax.</span></span>
                            <Toggle v-model="prefs.reduce_motion" />
                        </label>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                            <span><span class="block text-sm font-medium text-ink">Subtitles by default</span><span class="text-xs text-muted">Enable captions when trailers support them.</span></span>
                            <Toggle v-model="prefs.subtitles" />
                        </label>
                        <div class="rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                            <p class="mb-2 text-sm font-medium text-ink">Preferred language</p>
                            <SelectMenu v-model="prefs.language" :options="languageOptions" />
                        </div>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                            <span><span class="block text-sm font-medium text-ink">Allow adult content</span><span class="text-xs text-muted">Show adult titles in browse, search, and recommendations.</span></span>
                            <Toggle v-model="prefs.allow_adult" />
                        </label>
                    </div>

                    <AppButton class="mt-8" :disabled="prefs.processing" @click="savePrefs">Save playback settings</AppButton>
                </section>

                <!-- Billing -->
                <section v-else-if="activeTab === 'billing'" class="rounded-3xl glass p-6 sm:p-8">
                    <h2 class="font-display text-xl font-bold text-ink">Subscription</h2>
                    <p class="mt-1 text-sm text-muted">Manage your Flexter Premium plan.</p>

                    <div class="mt-8 rounded-2xl border border-hair/10 bg-surface2/40 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted">Current plan</p>
                                <p class="mt-2 font-display text-2xl font-bold text-ink">
                                    {{ isSubscribed ? (plan.name || 'Flexter Premium') : 'Free' }}
                                </p>
                                <p class="mt-1 text-sm text-muted">
                                    <template v-if="isSubscribed">
                                        Active — unlimited streaming included.
                                    </template>
                                    <template v-else>
                                        Streaming locked. Subscribe for {{ formattedPrice }}/month.
                                    </template>
                                </p>
                            </div>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide"
                                :class="isSubscribed ? 'bg-emerald-500/20 text-emerald-300' : 'bg-hair/15 text-muted'"
                            >
                                {{ isSubscribed ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <AppButton v-if="!isSubscribed" :href="route('billing.subscribe')">
                                Subscribe — {{ formattedPrice }}/mo
                            </AppButton>
                            <AppButton v-else :href="route('billing.portal')" variant="glass">
                                Manage billing
                            </AppButton>
                        </div>
                    </div>
                </section>

                <!-- History -->
                <section v-else-if="activeTab === 'history'" class="rounded-3xl glass p-6 sm:p-8">
                    <h2 class="font-display text-xl font-bold text-ink">Watch history</h2>
                    <p class="mt-1 text-sm text-muted">Every title you open is tracked. Mark items watched on detail pages.</p>
                    <div class="mt-8">
                        <div v-if="historyLoading" class="py-12 text-center text-sm text-muted">Loading history…</div>
                        <WatchHistoryList v-else :items="history" :stats="historyStatsLocal" />
                    </div>
                </section>

                <!-- Account -->
                <section v-else-if="activeTab === 'account'" class="rounded-3xl glass p-6 sm:p-8">
                    <h2 class="font-display text-xl font-bold text-ink">Account</h2>
                    <p class="mt-1 text-sm text-muted">Update your profile credentials.</p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ink">Name</label>
                            <input v-model="profile.name" type="text" class="w-full rounded-xl border border-hair/10 bg-surface2/50 px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-accent/40" />
                            <p v-if="profile.errors.name" class="mt-1 text-xs text-rose-400">{{ profile.errors.name }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ink">Email</label>
                            <input v-model="profile.email" type="email" class="w-full rounded-xl border border-hair/10 bg-surface2/50 px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-accent/40" />
                            <p v-if="profile.errors.email" class="mt-1 text-xs text-rose-400">{{ profile.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ink">New password</label>
                            <input v-model="profile.password" type="password" class="w-full rounded-xl border border-hair/10 bg-surface2/50 px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-accent/40" />
                            <p v-if="profile.errors.password" class="mt-1 text-xs text-rose-400">{{ profile.errors.password }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ink">Confirm password</label>
                            <input v-model="profile.password_confirmation" type="password" class="w-full rounded-xl border border-hair/10 bg-surface2/50 px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-accent/40" />
                        </div>
                    </div>

                    <AppButton class="mt-8" :disabled="profile.processing" @click="saveProfile">Update profile</AppButton>
                </section>

                <!-- Privacy -->
                <section v-else-if="activeTab === 'privacy'" class="space-y-6">
                    <div class="rounded-3xl glass p-6 sm:p-8">
                        <h2 class="font-display text-xl font-bold text-ink">Privacy &amp; notifications</h2>
                        <p class="mt-1 text-sm text-muted">Control spoilers and email preferences.</p>

                        <div class="mt-8 space-y-5">
                            <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                                <span><span class="block text-sm font-medium text-ink">Spoiler-free mode</span><span class="text-xs text-muted">Hide plot details on cards until you visit the page.</span></span>
                                <Toggle v-model="prefs.spoiler_free" />
                            </label>
                            <label class="flex items-center justify-between gap-4 rounded-2xl border border-hair/10 bg-surface2/30 p-4">
                                <span><span class="block text-sm font-medium text-ink">Email notifications</span><span class="text-xs text-muted">New releases and watchlist reminders.</span></span>
                                <Toggle v-model="prefs.email_notifications" />
                            </label>
                        </div>

                        <AppButton class="mt-8" :disabled="prefs.processing" @click="savePrefs">Save privacy settings</AppButton>
                    </div>

                    <div class="rounded-3xl border border-hair/10 bg-surface2/30 p-6 sm:p-8">
                        <h3 class="font-display text-lg font-bold text-ink">Data management</h3>
                        <p class="mt-2 text-sm text-muted">Manage your saved data from the watch history tab or watchlist page.</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <AppButton variant="ghost" @click="activeTab = 'history'">Manage watch history</AppButton>
                            <AppButton :href="route('watchlist')" variant="ghost">Manage watchlist</AppButton>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
