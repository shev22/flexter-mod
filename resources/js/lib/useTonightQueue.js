import { ref } from 'vue';

const STORAGE_KEY = 'flexter.tonight_queue';

function readQueue() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        const parsed = raw ? JSON.parse(raw) : [];
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

function writeQueue(items) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

const queue = ref(readQueue());

export function useTonightQueue() {
    function sync() {
        queue.value = readQueue();
    }

    function add(item) {
        const { type, id, title, poster } = item;
        if (!type || !id) return;

        const key = `${type}:${id}`;
        if (queue.value.some((entry) => `${entry.type}:${entry.id}` === key)) return;

        queue.value = [...queue.value, { type, id, title, poster }];
        writeQueue(queue.value);
    }

    function remove(type, id) {
        queue.value = queue.value.filter((entry) => !(entry.type === type && entry.id === id));
        writeQueue(queue.value);
    }

    function clear() {
        queue.value = [];
        writeQueue(queue.value);
    }

    function has(type, id) {
        return queue.value.some((entry) => entry.type === type && entry.id === id);
    }

    return { queue, add, remove, clear, has, sync };
}
