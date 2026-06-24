const STORAGE_KEY = 'flexter_watch_progress';

/**
 * Detect URL path style from the configured provider host.
 * - embed: https://vidsrc.to/embed/movie/{id}  (vidsrc.to API)
 * - legacy: https://vidsrc.ru/movie/{id}       (vidsrc.ru API)
 */
export function detectUrlStyle(baseUrl) {
    try {
        const host = new URL(baseUrl || 'https://vidsrc.to').hostname;

        if (host === 'vidsrc.ru' || host.endsWith('.vidsrc.ru')) {
            return 'legacy';
        }
    } catch {
        // fall through
    }

    return 'embed';
}

/**
 * Build an embed URL for the active streaming provider.
 *
 * @see https://vidsrc.to/ (embed style)
 * @see https://vidsrc.ru/docs (legacy style)
 */
export function buildVidsrcUrl({ type, id, season, episode, baseUrl, urlStyle }) {
    const base = (baseUrl || 'https://vidsrc.to').replace(/\/$/, '');
    const style = urlStyle || detectUrlStyle(base);

    if (style === 'embed') {
        if (type === 'tv') {
            return `${base}/embed/tv/${id}/${season}/${episode}`;
        }

        return `${base}/embed/movie/${id}`;
    }

    const path =
        type === 'tv'
            ? `${base}/tv/${id}/${season}/${episode}`
            : `${base}/movie/${id}`;

    const params = new URLSearchParams();
    params.set('autoplay', 'false');
    params.set('colour', accentColourHex());

    if (type === 'tv') {
        params.set('autonextepisode', 'true');
    }

    return `${path}?${params.toString()}`;
}

/**
 * Read the active accent colour as a hex string (no #) for legacy vidsrc.ru theming.
 */
export function accentColourHex() {
    const rgb = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim();
    const parts = rgb.split(/\s+/).map((n) => parseInt(n, 10));

    if (parts.length < 3 || parts.some((n) => Number.isNaN(n))) {
        return 'a855f7';
    }

    return parts.map((n) => n.toString(16).padStart(2, '0')).join('');
}

/**
 * Session-based progress when the provider does not expose postMessage tracking.
 * Estimates % from time spent in the player vs known runtime.
 */
export function createSessionProgressTracker({ runtime, type, onTick }) {
    const runtimeMinutes = runtime > 0 ? runtime : type === 'tv' ? 45 : 120;
    const runtimeMs = runtimeMinutes * 60 * 1000;
    const openedAt = Date.now();
    let intervalId = null;
    let lastPercent = 0;

    function elapsedPercent() {
        const elapsed = Date.now() - openedAt;

        return clampPercent((elapsed / runtimeMs) * 100);
    }

    function tick() {
        const percent = elapsedPercent();

        if (percent > lastPercent) {
            lastPercent = percent;
            onTick(percent);
        }
    }

    return {
        start() {
            tick();
            intervalId = window.setInterval(tick, 30_000);
        },
        stop() {
            if (intervalId) {
                window.clearInterval(intervalId);
            }

            const final = Math.max(lastPercent, elapsedPercent());
            lastPercent = final;

            return final;
        },
    };
}

export function isPlaybackOrigin(origin, baseUrl = 'https://vidsrc.to') {
    if (!origin) {
        return false;
    }

    try {
        const expected = new URL(baseUrl).hostname.replace(/^www\./, '');
        const actual = new URL(origin).hostname.replace(/^www\./, '');

        return actual === expected || actual.endsWith(`.${expected}`) || actual.includes('vidsrc');
    } catch {
        return origin.includes('vidsrc');
    }
}

/** @deprecated use isPlaybackOrigin */
export const isVidsrcOrigin = isPlaybackOrigin;

export function progressPercent(mediaData) {
    const progress = mediaData?.progress;

    if (progress == null) {
        return 0;
    }

    if (typeof progress === 'number') {
        return clampPercent(progress);
    }

    if (typeof progress.percent === 'number') {
        return clampPercent(progress.percent);
    }

    if (typeof progress.percentage === 'number') {
        return clampPercent(progress.percentage);
    }

    const watched = firstNumber(
        progress.watched,
        progress.current,
        progress.position,
        progress.time,
        progress.elapsed,
    );
    const duration = firstNumber(progress.duration, progress.total, progress.length);

    if (watched != null && duration != null && duration > 0) {
        return clampPercent((watched / duration) * 100);
    }

    return 0;
}

export function episodeContext(mediaData, fallback = {}) {
    const season = firstNumber(
        mediaData?.season,
        mediaData?.season_number,
        mediaData?.seasonNumber,
        fallback.season,
    );
    const episode = firstNumber(
        mediaData?.episode,
        mediaData?.episode_number,
        mediaData?.episodeNumber,
        fallback.episode,
    );

    return {
        season: season ?? null,
        episode: episode ?? null,
    };
}

/**
 * Optional: parse MEDIA_DATA postMessage events (vidsrc.ru only).
 */
export function parseVidsrcMessage(event, { baseUrl } = {}) {
    if (!isPlaybackOrigin(event.origin, baseUrl)) {
        return null;
    }

    const payload = event.data;

    if (payload?.type !== 'MEDIA_DATA' || !payload.data) {
        return null;
    }

    const mediaData = payload.data;
    const type = mediaData.type;

    if (!mediaData.id || (type !== 'movie' && type !== 'tv')) {
        return null;
    }

    return {
        id: String(mediaData.id),
        type,
        title: mediaData.title ?? null,
        progress: progressPercent(mediaData),
        ...episodeContext(mediaData),
        raw: mediaData,
    };
}

export function saveGuestSessionProgress({ type, id, season, episode, progress, title }) {
    const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    const key =
        type === 'tv' ? `${type}:${id}:${season ?? 0}:${episode ?? 0}` : `${type}:${id}`;

    stored[key] = {
        type,
        id: String(id),
        season: season ?? null,
        episode: episode ?? null,
        title: title ?? null,
        progress,
        last_updated: Date.now(),
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(stored));
}

/** @deprecated use saveGuestSessionProgress */
export function saveGuestProgress(mediaData) {
    if (!mediaData?.id) {
        return;
    }

    const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    stored[mediaData.id] = {
        ...stored[mediaData.id],
        ...mediaData,
        last_updated: Date.now(),
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(stored));
}

function clampPercent(value) {
    return Math.round(Math.min(100, Math.max(0, Number(value) || 0)));
}

function firstNumber(...values) {
    for (const value of values) {
        if (value != null && value !== '' && !Number.isNaN(Number(value))) {
            return Number(value);
        }
    }

    return null;
}
