import { ref } from 'vue';

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

export function useTonightQueue() {
    function sync() {
        queue.value = readQueue().items;
    }

    function add(item) {
        const { type, id, title, poster } = item;
        if (!type || !id) return;

        let { items, startedAt } = readQueue();
        const key = `${type}:${id}`;

        if (items.some((entry) => `${entry.type}:${entry.id}` === key)) return;

        if (items.length === 0) {
            startedAt = Date.now();
        }

        items = [...items, { type, id, title, poster }];
        queue.value = items;
        writeQueue(items, startedAt);
    }

    function remove(type, id) {
        let { items, startedAt } = readQueue();
        items = items.filter((entry) => !(entry.type === type && entry.id === id));

        if (items.length === 0) {
            startedAt = null;
        }

        queue.value = items;
        writeQueue(items, startedAt);
    }

    function clear() {
        queue.value = [];
        writeQueue([], null);
    }

    function has(type, id) {
        return readQueue().items.some((entry) => entry.type === type && entry.id === id);
    }

    return { queue, add, remove, clear, has, sync };
}
