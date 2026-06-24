<script setup>
import { computed, defineAsyncComponent, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '../Components/layout/Sidebar.vue';
import Navbar from '../Components/layout/Navbar.vue';
import AppFooter from '../Components/layout/AppFooter.vue';
import MobileTabBar from '../Components/layout/MobileTabBar.vue';
import KeyboardHelpModal from '../Components/layout/KeyboardHelpModal.vue';
import { applyAppearance } from '../lib/appearance.js';
import { useKeyboardShortcuts } from '../lib/useKeyboardShortcuts.js';

const SearchOverlay = defineAsyncComponent(() => import('../Components/layout/SearchOverlay.vue'));

const page = usePage();
const sidebarOpen = ref(false);
const searchOpen = ref(false);
const helpOpen = ref(false);
const toast = ref(null);
let toastTimer;

const HERO_PAGES = ['Home', 'Movies/Show', 'Tv/Show', 'Actors/Show'];
const hero = computed(() => HERO_PAGES.includes(page.component));

const showMaintenance = computed(
    () => page.props.site?.maintenance && !page.props.auth?.user?.is_admin,
);

useKeyboardShortcuts(helpOpen);

watch(
    () => page.props.settings,
    (settings) => applyAppearance(settings),
    { deep: true },
);

watch(
    () => page.props.flash,
    (flash) => {
        const message = flash?.success || flash?.error;
        if (!message) return;
        toast.value = { type: flash.success ? 'success' : 'error', message };
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => (toast.value = null), 3500);
    },
    { deep: true },
);

function onKey(e) {
    const tag = (e.target?.tagName || '').toLowerCase();
    const editing = tag === 'input' || tag === 'textarea' || e.target?.isContentEditable;
    if (e.key === '/' && !editing && !searchOpen.value) {
        e.preventDefault();
        searchOpen.value = true;
    }
}
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));
</script>

<template>
    <div class="min-h-screen bg-bg text-ink">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -left-40 -top-40 h-96 w-96 rounded-full bg-accent/10 blur-3xl" />
            <div class="absolute -right-40 top-1/3 h-96 w-96 rounded-full bg-accent2/10 blur-3xl" />
        </div>

        <div
            v-if="showMaintenance"
            class="fixed inset-x-0 top-0 z-[60] border-b border-amber-500/30 bg-amber-500/15 px-4 py-2.5 text-center text-sm font-medium text-amber-200 backdrop-blur-md"
        >
            {{ page.props.site?.name || 'Flexter' }} is in maintenance mode. Some features may be unavailable.
        </div>

        <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

        <div>
            <Navbar
                :hero="hero"
                :sidebar-open="sidebarOpen"
                :maintenance-offset="showMaintenance"
                @toggle-sidebar="sidebarOpen = !sidebarOpen"
                @open-search="searchOpen = true"
            />
            <main :class="[hero ? '' : 'pt-16', showMaintenance ? 'mt-10' : '', 'pb-20 lg:pb-0']">
                <slot />
                <AppFooter />
            </main>
        </div>

        <MobileTabBar @open-search="searchOpen = true" />
        <SearchOverlay v-if="searchOpen" @close="searchOpen = false" />
        <KeyboardHelpModal :open="helpOpen" @close="helpOpen = false" />

        <Transition
            enter-active-class="transition duration-300"
            enter-from-class="opacity-0 translate-y-3"
            leave-active-class="transition duration-200"
            leave-to-class="opacity-0"
        >
            <div
                v-if="toast"
                class="fixed bottom-24 left-1/2 z-[110] -translate-x-1/2 rounded-full glass-strong px-5 py-3 text-sm font-medium shadow-card lg:bottom-6"
                :class="toast.type === 'success' ? 'text-emerald-300' : 'text-rose-300'"
            >
                {{ toast.message }}
            </div>
        </Transition>
    </div>
</template>
