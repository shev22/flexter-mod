import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const STORAGE_KEY = 'flexter.tonight_queue';
const TTL_MS = 24 * 60 * 60 * 1000;

const queue = ref([]);

function isExpired(startedAt) {
    return startedAt != null && Date.now() - startedAt > TTL_MS;
}

function readLocal() {
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

function writeLocal(items, startedAt) {
    if (items.length === 0) {
        localStorage.removeItem(STORAGE_KEY);

        return;
    }

    localStorage.setItem(STORAGE_KEY, JSON.stringify({ items, startedAt }));
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

async function apiFetch(url, options = {}) {
    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers ?? {}),
        },
        credentials: 'same-origin',
        ...options,
    });

    if (!response.ok) {
        throw new Error('Tonight queue request failed');
    }

    return response.json();
}

export function useTonightQueue() {
    const page = usePage();
    const isAuth = () => !!page.props.auth?.user;

    function sync() {
        if (isAuth()) {
            return apiFetch(route('tonight.index')).then((data) => {
                queue.value = data.items ?? [];
            }).catch(() => {
                queue.value = readLocal().items;
            });
        }

        queue.value = readLocal().items;
    }

    function add(item) {
        const { type, id, title, poster } = item;
        if (!type || !id) return;

        if (isAuth()) {
            return apiFetch(route('tonight.toggle'), {
                method: 'POST',
                body: JSON.stringify({ type, id, title, poster }),
            }).then((data) => {
                queue.value = data.items ?? queue.value;
            });
        }

        let { items, startedAt } = readLocal();
        const key = `${type}:${id}`;

        if (items.some((entry) => `${entry.type}:${entry.id}` === key)) return;

        if (items.length === 0) {
            startedAt = Date.now();
        }

        items = [...items, { type, id, title, poster }];
        queue.value = items;
        writeLocal(items, startedAt);
    }

    function remove(type, id) {
        if (isAuth()) {
            return apiFetch(route('tonight.destroy'), {
                method: 'DELETE',
                body: JSON.stringify({ type, id }),
            }).then((data) => {
                queue.value = data.items ?? [];
            });
        }

        let { items, startedAt } = readLocal();
        items = items.filter((entry) => !(entry.type === type && entry.id === id));

        if (items.length === 0) {
            startedAt = null;
        }

        queue.value = items;
        writeLocal(items, startedAt);
    }

    function clear() {
        if (isAuth()) {
            return apiFetch(route('tonight.clear'), { method: 'DELETE' }).then((data) => {
                queue.value = data.items ?? [];
            });
        }

        queue.value = [];
        writeLocal([], null);
    }

    function has(type, id) {
        return queue.value.some((entry) => entry.type === type && entry.id === id);
    }

    async function pickRandom(source = 'watchlist') {
        if (!isAuth()) {
            const { items } = readLocal();

            if (items.length === 0) {
                return { picked: null, items: [] };
            }

            const pick = items[Math.floor(Math.random() * items.length)];

            return { picked: pick, items };
        }

        const data = await apiFetch(route('tonight.pick'), {
            method: 'POST',
            body: JSON.stringify({ source }),
        });

        queue.value = data.items ?? queue.value;

        return data;
    }

    return { queue, add, remove, clear, has, sync, pickRandom };
}
