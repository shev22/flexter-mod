<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { HeartIcon as HeartSolid } from '@heroicons/vue/24/solid';
import { HeartIcon as HeartOutline } from '@heroicons/vue/24/outline';
import { actorRoute } from '../../lib/format.js';
import { useWatchlist } from '../../lib/useWatchlist.js';

const props = defineProps({
    person: { type: Object, required: true },
});

const { toggleActor } = useWatchlist();
const href = computed(() => actorRoute(props.person));
const initials = computed(() =>
    (props.person.name || '?')
        .split(' ')
        .slice(0, 2)
        .map((n) => n[0])
        .join('')
        .toUpperCase(),
);

function follow(event) {
    event?.stopPropagation();
    event?.preventDefault();
    toggleActor(props.person.id, props.person.in_watchlist, (value) => {
        props.person.in_watchlist = value;
    });
}
</script>

<template>
    <div class="group relative shrink-0 text-center">
        <div class="relative mx-auto w-full">
            <Link :href="href" class="block">
                <div
                    class="relative aspect-[3/4] w-full overflow-hidden rounded-2xl bg-surface2 ring-1 ring-hair/10 transition-all duration-300 group-hover:-translate-y-1 group-hover:ring-accent/70"
                >
                    <img
                        v-if="person.profile"
                        :src="person.profile"
                        :alt="person.name"
                        loading="lazy"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                    <div v-else class="grid h-full w-full place-items-center bg-aurora-soft font-display text-2xl text-ink">
                        {{ initials }}
                    </div>
                </div>
            </Link>

            <button
                type="button"
                class="absolute right-2 top-2 z-10 flex items-center gap-1 rounded-full px-2.5 py-1.5 text-[11px] font-semibold backdrop-blur transition"
                :class="person.in_watchlist
                    ? 'bg-aurora text-white shadow-glow'
                    : 'bg-black/55 text-white hover:bg-accent'"
                :aria-label="person.in_watchlist ? 'Unfollow actor' : 'Follow actor'"
                @click="follow"
            >
                <HeartSolid v-if="person.in_watchlist" class="h-3.5 w-3.5" />
                <HeartOutline v-else class="h-3.5 w-3.5" />
                <span>{{ person.in_watchlist ? 'Following' : 'Follow' }}</span>
            </button>
        </div>

        <Link :href="href" class="mt-3 block">
            <p class="line-clamp-1 text-sm font-semibold text-ink transition group-hover:text-accent">
                {{ person.name }}
            </p>
            <p v-if="person.known_for" class="line-clamp-1 text-xs text-muted">{{ person.known_for }}</p>
        </Link>
    </div>
</template>
