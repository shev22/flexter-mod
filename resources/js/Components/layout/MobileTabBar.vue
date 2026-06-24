<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    HomeIcon,
    MagnifyingGlassIcon,
    BookmarkIcon,
    Cog6ToothIcon,
} from '@heroicons/vue/24/outline';
import {
    HomeIcon as HomeSolid,
    MagnifyingGlassIcon as SearchSolid,
    BookmarkIcon as BookmarkSolid,
    Cog6ToothIcon as SettingsSolid,
} from '@heroicons/vue/24/solid';

const emit = defineEmits(['open-search']);
const page = usePage();
const auth = computed(() => page.props.auth?.user);

const tabs = computed(() => [
    { key: 'home', label: 'Home', route: 'home', match: ['home'], icon: HomeIcon, iconActive: HomeSolid, auth: false },
    { key: 'search', label: 'Search', action: 'search', match: ['search.show'], icon: MagnifyingGlassIcon, iconActive: SearchSolid, auth: false },
    { key: 'watchlist', label: 'Watchlist', route: 'watchlist', match: ['watchlist'], icon: BookmarkIcon, iconActive: BookmarkSolid, auth: true },
    { key: 'settings', label: 'Settings', route: 'settings', match: ['settings'], icon: Cog6ToothIcon, iconActive: SettingsSolid, auth: true },
]);

function isActive(tab) {
    return tab.match.some((pattern) => route().current(pattern));
}

function onTab(tab) {
    if (tab.action === 'search') {
        emit('open-search');
    }
}
</script>

<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-30 border-t border-hair/10 bg-bg/90 pb-[env(safe-area-inset-bottom)] backdrop-blur-xl lg:hidden"
        aria-label="Mobile navigation"
    >
        <div class="flex h-16 items-stretch justify-around px-2">
            <template v-for="tab in tabs" :key="tab.key">
                <Link
                    v-if="tab.route && (!tab.auth || auth)"
                    :href="route(tab.route)"
                    class="flex flex-1 flex-col items-center justify-center gap-0.5 text-[10px] font-semibold transition"
                    :class="isActive(tab) ? 'text-accent' : 'text-muted'"
                >
                    <component :is="isActive(tab) ? tab.iconActive : tab.icon" class="h-6 w-6" />
                    {{ tab.label }}
                </Link>
                <Link
                    v-else-if="tab.route && tab.auth && !auth"
                    :href="route('login')"
                    class="flex flex-1 flex-col items-center justify-center gap-0.5 text-[10px] font-semibold text-muted transition"
                >
                    <component :is="tab.icon" class="h-6 w-6" />
                    {{ tab.label }}
                </Link>
                <button
                    v-else-if="tab.action === 'search'"
                    type="button"
                    class="flex flex-1 flex-col items-center justify-center gap-0.5 text-[10px] font-semibold transition"
                    :class="isActive(tab) ? 'text-accent' : 'text-muted'"
                    @click="onTab(tab)"
                >
                    <component :is="isActive(tab) ? tab.iconActive : tab.icon" class="h-6 w-6" />
                    {{ tab.label }}
                </button>
            </template>
        </div>
    </nav>
</template>
