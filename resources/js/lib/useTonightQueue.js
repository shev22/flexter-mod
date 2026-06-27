import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { clearGuestPayload } from './guestMerge.js';

const STORAGE_KEY = 'flexter.tonight_queue';
const TTL_MS = 24 * 60 * 60 * 1000;

function isExpired(startedAt) {
    return startedAt != null && Date.now() - startedAt > TTL_MS;
}

function readQueue() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
            return { items: [], startedAt: null };
        }

        const parsed = JSON.parse(raw);

        if (Array.isArray(parsed)) {
            if (parsed.length === 0) {
                return { items: [], startedAt: null };
            }

            const migrated = { items: parsed, startedAt: Date.now() };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(migrated));

            return migrated;
        }

        const items = Array.isArray(parsed.items) ? parsed.items : [];
        const startedAt = parsed.startedAt ?? null;

        if (isExpired(startedAt)) {
            localStorage.removeItem(STORAGE_KEY);

            return { items: [], startedAt: null };
        }

        return { items, startedAt };
    } catch {
        return { items: [], startedAt: null };
    }
}

function writeQueue(items, startedAt) {
    if (items.length === 0) {
        localStorage.removeItem(STORAGE_KEY);

        return;
    }

    localStorage.setItem(STORAGE_KEY, JSON.stringify({ items, startedAt }));
}

const queue = ref(readQueue().items);
let serverLoaded = false;

function entryKey(type, id) {
    return `${type}:${String(id)}`;
}

function isInQueue(items, type, id) {
    const key = entryKey(type, id);

    return items.some((entry) => entryKey(entry.type, entry.id) === key);
}

function isAuthed() {
    return Boolean(usePage().props.auth?.user);
}

async function loadFromServer() {
    if (!isAuthed() || serverLoaded) {
        return;
    }

    try {
        const { data } = await window.axios.get(route('tonight-queue.index'));
        queue.value = data.items ?? [];
        serverLoaded = true;
        localStorage.removeItem(STORAGE_KEY);
    } catch {
        queue.value = readQueue().items;
    }
}

export function useTonightQueue() {
    if (isAuthed()) {
        loadFromServer();
    }

    function sync() {
        if (isAuthed()) {
            loadFromServer();

            return;
        }

        queue.value = readQueue().items;
    }

    async function add(item) {
        const { type, id, title, poster } = item;
        if (!type || !id) return;

        if (isAuthed()) {
            if (has(type, id)) {
                return;
            }

            const { data } = await window.axios.post(route('tonight-queue.toggle'), {
                type,
                id,
                title,
                poster,
            });

            if (data.added) {
                queue.value = data.items ?? [];
            }

            return;
        }

        let { items, startedAt } = readQueue();

        if (isInQueue(items, type, id)) return;

        if (items.length === 0) {
            startedAt = Date.now();
        }

        items = [...items, { type, id, title, poster }];
        queue.value = items;
        writeQueue(items, startedAt);
    }

    async function remove(type, id) {
        if (isAuthed()) {
            const { data } = await window.axios.delete(route('tonight-queue.destroy'), {
                data: { type, id },
            });
            queue.value = data.items ?? [];

            return;
        }

        let { items, startedAt } = readQueue();
        items = items.filter((entry) => entryKey(entry.type, entry.id) !== entryKey(type, id));

        if (items.length === 0) {
            startedAt = null;
        }

        queue.value = items;
        writeQueue(items, startedAt);
    }

    async function clear() {
        if (isAuthed()) {
            const { data } = await window.axios.delete(route('tonight-queue.clear'));
            queue.value = data.items ?? [];

            return;
        }

        queue.value = [];
        writeQueue([], null);
    }

    function has(type, id) {
        return isInQueue(queue.value, type, id);
    }

    async function toggle(item) {
        const { type, id, title, poster } = item;
        if (!type || !id) return false;

        if (isAuthed()) {
            const { data } = await window.axios.post(route('tonight-queue.toggle'), {
                type,
                id,
                title,
                poster,
            });
            queue.value = data.items ?? [];
            clearGuestPayload();

            return Boolean(data.added);
        }

        if (has(type, id)) {
            await remove(type, id);

            return false;
        }

        await add({ type, id, title, poster });

        return true;
    }

    return { queue, add, remove, toggle, clear, has, sync };
}
