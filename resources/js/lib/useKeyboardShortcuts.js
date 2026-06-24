import { onBeforeUnmount, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const GO_ROUTES = {
    h: 'home',
    m: 'movies',
    t: 'tv',
    w: 'watchlist',
    s: 'settings',
};

/**
 * Register global keyboard shortcuts. Pass a ref whose `.value` toggles the help modal.
 */
export function useKeyboardShortcuts(helpOpenRef) {
    let awaitingGo = false;
    let goTimer;

    function isEditing(target) {
        const tag = (target?.tagName || '').toLowerCase();
        return tag === 'input' || tag === 'textarea' || target?.isContentEditable;
    }

    function resetGo() {
        awaitingGo = false;
        clearTimeout(goTimer);
    }

    function onKeydown(e) {
        if (isEditing(e.target)) return;

        if (e.key === '?' && !e.metaKey && !e.ctrlKey && !e.altKey) {
            e.preventDefault();
            if (helpOpenRef) helpOpenRef.value = !helpOpenRef.value;
            return;
        }

        if (e.key === 'Escape' && helpOpenRef?.value) {
            helpOpenRef.value = false;
            return;
        }

        if (e.key === 'g' && !e.metaKey && !e.ctrlKey && !e.altKey) {
            if (!awaitingGo) {
                awaitingGo = true;
                clearTimeout(goTimer);
                goTimer = setTimeout(resetGo, 800);
            }
            return;
        }

        if (awaitingGo && GO_ROUTES[e.key]) {
            e.preventDefault();
            const name = GO_ROUTES[e.key];
            resetGo();
            router.visit(route(name));
        }
    }

    onMounted(() => window.addEventListener('keydown', onKeydown));
    onBeforeUnmount(() => {
        window.removeEventListener('keydown', onKeydown);
        resetGo();
    });
}
