import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export function useWatchHistory() {
    function touch(type, id, progress = 15) {
        router.post(
            route('history.touch'),
            { type, id, progress },
            { preserveScroll: true, preserveState: true },
        );
    }

    function markWatched(type, id, onSuccess, season = null, episode = null) {
        const payload = { type, id };
        if (season != null) payload.season = season;
        if (episode != null) payload.episode = episode;

        router.post(route('history.mark'), payload, {
            preserveScroll: true,
            preserveState: true,
            onSuccess,
        });
    }

    function bump(type, id, progress, season = null, episode = null) {
        const payload = { type, id, progress };
        if (season != null) payload.season = season;
        if (episode != null) payload.episode = episode;

        router.post(route('history.bump'), payload, { preserveScroll: true, preserveState: true });
    }

    function remove(id) {
        router.delete(route('history.destroy', id), { preserveScroll: true });
    }

    function clearAll() {
        router.delete(route('history.clear'), { preserveScroll: true });
    }

    return { touch, markWatched, bump, remove, clearAll };
}
