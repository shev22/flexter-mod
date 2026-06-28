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
let sidebarCloseTimer;

const HERO_PAGES = ['Home', 'Movies/Show', 'Tv/Show', 'Actors/Show'];
const hero = computed(() => HERO_PAGES.includes(page.component));

const isLight = computed(() => {
    const theme = page.props.settings?.theme ?? 'dark';

    if (theme === 'light') {
        return true;
    }

    if (theme === 'dark') {
        return false;
    }

    return typeof window !== 'undefined'
        && window.matchMedia?.('(prefers-color-scheme: light)').matches;
});

useKeyboardShortcuts(helpOpen);

watch(
    () => page.props.settings,
    (settings) => applyAppearance(settings),
    { deep: true, immediate: true },
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

function openSidebar() {
    clearTimeout(sidebarCloseTimer);
    sidebarOpen.value = true;
}

function scheduleCloseSidebar() {
    clearTimeout(sidebarCloseTimer);
    sidebarCloseTimer = setTimeout(() => {
        sidebarOpen.value = false;
    }, 280);
}

function onLeftEdgeEnter() {
    if (window.matchMedia('(min-width: 1024px)').matches) {
        openSidebar();
    }
}

function onKey(e) {
    const tag = (e.target?.tagName || '').toLowerCase();
    const editing = tag === 'input' || tag === 'textarea' || e.target?.isContentEditable;
    if (e.key === '/' && !editing && !searchOpen.value) {
        e.preventDefault();
        searchOpen.value = true;
    }
}
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKey);
    clearTimeout(sidebarCloseTimer);
});
</script>

<template>
    <div class="min-h-screen bg-bg text-ink">
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div
                class="absolute -left-40 -top-40 h-96 w-96 rounded-full blur-3xl"
                :class="isLight ? 'bg-amber-200/30' : 'bg-accent/10'"
            />
            <div
                class="absolute -right-40 top-1/3 h-96 w-96 rounded-full blur-3xl"
                :class="isLight ? 'bg-orange-100/40' : 'bg-accent2/10'"
            />
        </div>

        <div
            class="fixed inset-y-0 left-0 z-[45] hidden w-3 lg:block"
            aria-hidden="true"
            @mouseenter="onLeftEdgeEnter"
        />

        <Sidebar
            :open="sidebarOpen"
            @close="sidebarOpen = false"
            @mouseenter="openSidebar"
            @mouseleave="scheduleCloseSidebar"
        />

        <div>
            <Navbar
                :hero="hero"
                :sidebar-open="sidebarOpen"
                @toggle-sidebar="sidebarOpen = !sidebarOpen"
                @open-search="searchOpen = true"
            />
            <main :class="[hero ? '' : 'pt-16', 'pb-20 lg:pb-0']">
                <div :key="page.url">
                    <slot />
                </div>
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
