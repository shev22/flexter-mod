const PROGRESS_KEY = 'flexter_watch_progress';
const TONIGHT_KEY = 'flexter.tonight_queue';

function readTonightLocal() {
    try {
        const raw = localStorage.getItem(TONIGHT_KEY);
        if (!raw) {
            return { items: [], startedAt: null };
        }

        const parsed = JSON.parse(raw);

        if (Array.isArray(parsed)) {
            return { items: parsed, startedAt: Date.now() };
        }

        return {
            items: Array.isArray(parsed.items) ? parsed.items : [],
            startedAt: parsed.startedAt ?? null,
        };
    } catch {
        return { items: [], startedAt: null };
    }
}

export function collectGuestPayload() {
    let progress = {};

    try {
        progress = JSON.parse(localStorage.getItem(PROGRESS_KEY) || '{}');
    } catch {
        progress = {};
    }

    const tonight = readTonightLocal();

    const hasProgress = Object.keys(progress).length > 0;
    const hasTonight = tonight.items.length > 0;

    if (!hasProgress && !hasTonight) {
        return null;
    }

    return {
        progress: hasProgress ? progress : {},
        tonight: hasTonight ? tonight : null,
    };
}

export function clearGuestPayload() {
    localStorage.removeItem(PROGRESS_KEY);
    localStorage.removeItem(TONIGHT_KEY);
}

export function guestDataField() {
    const payload = collectGuestPayload();

    return payload ? JSON.stringify(payload) : null;
}
