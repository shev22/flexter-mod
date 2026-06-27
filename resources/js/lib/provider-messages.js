import {
    episodeContext,
    isPlaybackOrigin,
    progressPercent,
} from './vidsrc.js';

/**
 * Parse postMessage events from VidPhantom, VidSrc.ru, and related embed players.
 *
 * @see https://vidphantom.com/
 */
export function parsePlaybackMessage(event, { baseUrl, type, id, season, episode } = {}) {
    if (!isPlaybackOrigin(event.origin, baseUrl)) {
        return null;
    }

    const payload = event.data;

    if (!payload || typeof payload !== 'object') {
        return null;
    }

    if (payload.type === 'PLAYER_EVENT' && payload.data) {
        return parsePlayerEvent(payload.data);
    }

    if (payload.type === 'MEDIA_DATA' && payload.data) {
        return parseMediaData(payload.data, { type, id, season, episode });
    }

    return null;
}

function parsePlayerEvent(data) {
    const mediaType = data.mediaType === 'tv' || data.type === 'tv' ? 'tv' : 'movie';
    const mediaId = data.tmdbId ?? data.id;

    if (mediaId == null) {
        return null;
    }

    let progress = 0;

    if (data.duration > 0 && data.currentTime != null) {
        progress = clampPercent((Number(data.currentTime) / Number(data.duration)) * 100);
    } else if (data.progress != null) {
        progress = clampPercent(data.progress);
    }

    if (progress <= 0 && data.event !== 'ended') {
        return null;
    }

    // Some providers fire "ended" on iframe unload — only treat as finished near the end.
    if (data.event === 'ended' && progress >= 90) {
        progress = 100;
    }

    return {
        id: String(mediaId),
        type: mediaType,
        progress,
        season: data.season ?? null,
        episode: data.episode ?? null,
    };
}

function parseMediaData(data, { type, id, season, episode }) {
    let mediaData = data;

    if (!mediaData.id && typeof mediaData === 'object') {
        mediaData = resolveMediaDataFromStore(mediaData, { type, id, season, episode });
    }

    if (!mediaData?.id) {
        return null;
    }

    const mediaType = mediaData.type;

    if (mediaType !== 'movie' && mediaType !== 'tv') {
        return null;
    }

    return {
        id: String(mediaData.id),
        type: mediaType,
        title: mediaData.title ?? null,
        progress: progressPercent(mediaData),
        ...episodeContext(mediaData, { season, episode }),
        raw: mediaData,
    };
}

function resolveMediaDataFromStore(store, { type, id, season, episode }) {
    const direct = store[String(id)] ?? store[Number(id)];

    if (direct) {
        if (type === 'tv' && season != null && episode != null && direct.show_progress) {
            const key = `s${season}e${episode}`;
            const ep = direct.show_progress[key];

            if (ep?.progress) {
                return {
                    ...direct,
                    season: ep.season ?? season,
                    episode: ep.episode ?? episode,
                    progress: ep.progress,
                };
            }
        }

        return direct;
    }

    for (const item of Object.values(store)) {
        if (item && String(item.id) === String(id) && item.type === type) {
            return item;
        }
    }

    return null;
}

function clampPercent(value) {
    return Math.round(Math.min(100, Math.max(0, Number(value) || 0)));
}
