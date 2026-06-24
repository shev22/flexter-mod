import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

/**
 * Optimistic watchlist + favourite toggles backed by Inertia POSTs. Guests are
 * bounced to the login screen.
 */
export function useWatchlist() {
    const page = usePage();
    const pending = ref(false);

    function isGuest() {
        if (!page.props.auth?.user) {
            router.visit(route('login'));
            return true;
        }
        return false;
    }

    function toggle(item) {
        if (isGuest()) return;

        const adding = !item.in_watchlist;
        const name =
            item.type === 'tv'
                ? adding
                    ? 'tv.add.watchlist'
                    : 'tv.remove.watchlist'
                : adding
                  ? 'movie.add.watchlist'
                  : 'movie.remove.watchlist';

        item.in_watchlist = adding;
        pending.value = true;

        router.post(
            route(name),
            { id: item.id },
            {
                preserveScroll: true,
                preserveState: true,
                onError: () => {
                    item.in_watchlist = !adding;
                },
                onFinish: () => {
                    pending.value = false;
                },
            },
        );
    }

    function toggleActor(actorId, currentlyFavorite, onChange) {
        if (isGuest()) return;

        const adding = !currentlyFavorite;
        onChange?.(adding);

        router.post(
            route(adding ? 'actor.favorite' : 'actor.unfavorite'),
            { id: actorId },
            {
                preserveScroll: true,
                preserveState: true,
                onError: () => onChange?.(!adding),
            },
        );
    }

    return { toggle, toggleActor, pending };
}
