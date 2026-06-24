/**
 * Normalize a TMDB YouTube key or full URL into an 11-char video id.
 */
export function extractYouTubeKey(input) {
    if (input === null || input === undefined || input === '') return null;

    const trimmed = String(input).trim();
    if (/^[a-zA-Z0-9_-]{11}$/.test(trimmed)) {
        return trimmed;
    }

    try {
        const url = new URL(trimmed);
        if (url.hostname.includes('youtu.be')) {
            const id = url.pathname.replace(/^\//, '').split('/')[0] || null;
            return id && /^[a-zA-Z0-9_-]{11}$/.test(id) ? id : null;
        }
        const fromQuery = url.searchParams.get('v');
        if (fromQuery && /^[a-zA-Z0-9_-]{11}$/.test(fromQuery)) return fromQuery;
        const embedMatch = url.pathname.match(/\/embed\/([^/?]+)/);
        if (embedMatch?.[1] && /^[a-zA-Z0-9_-]{11}$/.test(embedMatch[1])) return embedMatch[1];
    } catch {
        // Not a URL — fall through.
    }

    return null;
}

function buildEmbedUrl(id, params) {
    return `https://www.youtube.com/embed/${id}?${params.toString()}`;
}

/**
 * Build a YouTube embed URL for muted background playback.
 */
export function trailerEmbedUrl(key, { autoplay = true } = {}) {
    const id = extractYouTubeKey(key);
    if (!id) return null;

    const params = new URLSearchParams({
        autoplay: autoplay ? '1' : '0',
        mute: '1',
        controls: '0',
        loop: '1',
        playlist: id,
        modestbranding: '1',
        playsinline: '1',
        rel: '0',
        iv_load_policy: '3',
        enablejsapi: '1',
    });

    if (typeof window !== 'undefined' && window.location?.origin) {
        params.set('origin', window.location.origin);
    }

    return buildEmbedUrl(id, params);
}

/**
 * Build a YouTube embed URL for the fullscreen modal player.
 */
export function trailerModalEmbedUrl(key, settings = {}) {
    const id = extractYouTubeKey(key);
    if (!id) return null;

    const params = new URLSearchParams({
        autoplay: '1',
        rel: '0',
        modestbranding: '1',
        playsinline: '1',
        iv_load_policy: '3',
        enablejsapi: '1',
    });

    if (settings?.subtitles) {
        params.set('cc_load_policy', '1');
    }

    if (typeof window !== 'undefined' && window.location?.origin) {
        params.set('origin', window.location.origin);
    }

    return buildEmbedUrl(id, params);
}

/**
 * Whether background / hover trailers should play based on user settings.
 */
export function allowBackgroundTrailer(settings) {
    return settings?.autoplay_trailers !== false;
}
