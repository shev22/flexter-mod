<script setup>
import { computed, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { HeartIcon, MapPinIcon, CakeIcon, MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { useWatchlist } from '../../lib/useWatchlist.js';
import { filterMediaItems, ratingOptions, yearOptionsFromItems } from '../../lib/filters.js';
import MediaCard from '../../Components/ui/MediaCard.vue';
import MultiSelectMenu from '../../Components/ui/MultiSelectMenu.vue';
import EmptyState from '../../Components/ui/EmptyState.vue';

const props = defineProps({
    actor: { type: Object, required: true },
    is_favorite: { type: Boolean, default: false },
});

const { toggleActor } = useWatchlist();
const favorite = ref(props.is_favorite);

const tab = ref('movies');
const query = ref('');
const years = ref([]);
const ratings = ref([]);
const bioExpanded = ref(false);

const list = computed(() => (tab.value === 'movies' ? props.actor.movies : props.actor.tv));

const yearOptions = computed(() => yearOptionsFromItems(list.value));

const hasFilmographyFilters = computed(() => query.value || years.value.length || ratings.value.length);

const filtered = computed(() =>
    filterMediaItems(list.value, {
        search: query.value,
        years: years.value,
        ratings: ratings.value,
    }),
);

const socials = computed(() => {
    const s = props.actor.socials || {};
    const links = [];
    if (s.instagram) links.push({ label: 'Instagram', url: `https://instagram.com/${s.instagram}` });
    if (s.twitter) links.push({ label: 'Twitter / X', url: `https://twitter.com/${s.twitter}` });
    if (s.facebook) links.push({ label: 'Facebook', url: `https://facebook.com/${s.facebook}` });
    if (s.imdb) links.push({ label: 'IMDb', url: `https://www.imdb.com/name/${s.imdb}` });
    return links;
});

const bioShort = computed(() => {
    const bio = props.actor.biography || '';
    return bio.length > 480 && !bioExpanded.value ? bio.slice(0, 480) + '…' : bio;
});

function fav() {
    toggleActor(props.actor.id, favorite.value, (v) => (favorite.value = v));
}

function resetFilmographyFilters() {
    query.value = '';
    years.value = [];
    ratings.value = [];
}

watch(tab, resetFilmographyFilters);
</script>

<template>
    <Head :title="actor.name" />

    <div class="relative">
        <!-- ambient header -->
        <div class="absolute inset-x-0 top-0 h-80 overflow-hidden">
            <div class="h-full w-full bg-aurora-soft" />
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-bg" />
        </div>

        <div class="relative px-4 pt-16 lg:px-12">
            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- profile -->
                <div class="shrink-0">
                    <div class="mx-auto w-48 overflow-hidden rounded-3xl shadow-card ring-1 ring-hair/10 lg:w-64">
                        <img v-if="actor.profile" :src="actor.profile" :alt="actor.name" class="aspect-[2/3] w-full object-cover" />
                        <div v-else class="grid aspect-[2/3] w-full place-items-center bg-surface2 font-display text-4xl text-muted">{{ actor.name?.[0] }}</div>
                    </div>

                    <button
                        type="button"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-full px-5 py-3 font-semibold transition"
                        :class="favorite ? 'bg-aurora text-white shadow-glow' : 'glass-strong text-ink hover:bg-hair/10'"
                        @click="fav"
                    >
                        <HeartIcon class="h-5 w-5" :class="favorite ? 'text-white' : 'text-accent'" />
                        {{ favorite ? 'Following' : 'Add to favourites' }}
                    </button>

                    <div v-if="socials.length" class="mt-4 space-y-2">
                        <a v-for="link in socials" :key="link.label" :href="link.url" target="_blank" rel="noopener" class="block rounded-xl glass px-4 py-2.5 text-center text-sm font-medium text-ink transition hover:text-accent">
                            {{ link.label }}
                        </a>
                    </div>
                </div>

                <!-- details -->
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent">{{ actor.known_for_department || 'Acting' }}</p>
                    <h1 class="mt-2 font-display text-4xl font-extrabold text-ink sm:text-5xl">{{ actor.name }}</h1>

                    <div class="mt-4 flex flex-wrap gap-5 text-sm text-muted">
                        <span v-if="actor.birthday" class="flex items-center gap-1.5"><CakeIcon class="h-4 w-4 text-accent" /> {{ actor.birthday }}<template v-if="actor.age"> · {{ actor.age }} yrs</template></span>
                        <span v-if="actor.place_of_birth" class="flex items-center gap-1.5"><MapPinIcon class="h-4 w-4 text-accent" /> {{ actor.place_of_birth }}</span>
                        <span>{{ actor.gender }}</span>
                    </div>

                    <div v-if="actor.biography" class="mt-6">
                        <h2 class="mb-2 font-display text-lg font-bold text-ink">Biography</h2>
                        <p class="whitespace-pre-line text-sm leading-relaxed text-muted">{{ bioShort }}</p>
                        <button v-if="(actor.biography || '').length > 480" type="button" class="mt-2 text-sm font-semibold text-accent" @click="bioExpanded = !bioExpanded">
                            {{ bioExpanded ? 'Show less' : 'Read more' }}
                        </button>
                    </div>

                    <div v-if="actor.images?.length" class="mt-8">
                        <h2 class="mb-3 font-display text-lg font-bold text-ink">Photos</h2>
                        <div class="no-scrollbar flex gap-3 overflow-x-auto pb-2">
                            <img v-for="(img, i) in actor.images" :key="i" :src="img" :alt="actor.name" class="h-44 w-32 shrink-0 rounded-xl object-cover ring-1 ring-hair/10" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- filmography -->
            <div class="mt-14 pb-16">
                <div class="mb-6 flex flex-col gap-4">
                    <div class="inline-flex w-fit rounded-full glass p-1">
                        <button type="button" class="rounded-full px-5 py-2 text-sm font-semibold transition" :class="tab === 'movies' ? 'bg-aurora text-white' : 'text-muted hover:text-ink'" @click="tab = 'movies'">
                            Movies ({{ actor.movies.length }})
                        </button>
                        <button type="button" class="rounded-full px-5 py-2 text-sm font-semibold transition" :class="tab === 'tv' ? 'bg-aurora text-white' : 'text-muted hover:text-ink'" @click="tab = 'tv'">
                            Series ({{ actor.tv.length }})
                        </button>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                        <div class="relative flex-1 sm:min-w-[14rem]">
                            <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" />
                            <input v-model="query" type="text" placeholder="Search filmography…" class="w-full rounded-xl glass py-2.5 pl-11 pr-4 text-sm text-ink placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-accent" />
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:flex">
                            <div class="sm:w-36">
                                <MultiSelectMenu v-model="years" :options="yearOptions" placeholder="Years" />
                            </div>
                            <div class="sm:w-36">
                                <MultiSelectMenu v-model="ratings" :options="ratingOptions" placeholder="Rating" />
                            </div>
                            <button
                                v-if="hasFilmographyFilters"
                                type="button"
                                class="col-span-2 inline-flex items-center justify-center gap-1.5 rounded-xl glass px-3 py-2.5 text-sm font-medium text-muted transition hover:text-accent sm:col-span-1"
                                @click="resetFilmographyFilters"
                            >
                                <XMarkIcon class="h-4 w-4" /> Clear
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="filtered.length" class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                    <MediaCard v-for="item in filtered" :key="`${item.type}-${item.id}`" :item="item" />
                </div>
                <EmptyState v-else title="Nothing to show here">
                    <template #icon><MagnifyingGlassIcon class="h-7 w-7" /></template>
                </EmptyState>
            </div>
        </div>
    </div>
</template>
