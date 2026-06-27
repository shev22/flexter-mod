import {
    accentColourHex,
    buildVidsrcUrl,
    createSessionProgressTracker,
    isPlaybackOrigin,
    parseVidsrcMessage,
    saveGuestSessionProgress,
} from './vidsrc.js';

export { parsePlaybackMessage } from './provider-messages.js';

export {
    accentColourHex,
    buildVidsrcUrl,
    createSessionProgressTracker,
    isPlaybackOrigin,
    parseVidsrcMessage,
    saveGuestSessionProgress,
};

/**
 * Build an embed URL for the active streaming provider.
 *
 * @see https://vidplus.to
 * @see https://vidphantom.com
 */
export function buildPlaybackUrl({
    type,
    id,
    season,
    episode,
    playback = {},
    progressPercent = 0,
    runtime = null,
}) {
    const provider = playback.provider ?? 'vidplus';

    if (provider === 'disabled' || playback.enabled === false) {
        throw new Error('Streaming is disabled.');
    }

    if (provider === 'vidsrc') {
        return buildVidsrcUrl({
            type,
            id,
            season,
            episode,
            baseUrl: playback.base_url,
            urlStyle: playback.url_style,
        });
    }

    if (provider === 'vidphantom') {
        return buildVidphantomUrl({
            type,
            id,
            season,
            episode,
            playback,
            progressPercent,
            runtime,
        });
    }

    return buildVidplusUrl({
        type,
        id,
        season,
        episode,
        playback,
        progressPercent,
        runtime,
    });
}

/**
 * VidPlus embed URLs — https://player.vidplus.to/embed/movie/{tmdbId}
 */
export function buildVidplusUrl({ type, id, season, episode, playback, progressPercent, runtime }) {
    const base = (playback.base_url || 'https://player.vidplus.to').replace(/\/$/, '');

    const path =
        type === 'tv'
            ? `${base}/embed/tv/${id}/${season}/${episode}`
            : `${base}/embed/movie/${id}`;

    const params = new URLSearchParams();

    setBool(params, 'autoplay', playback.autoplay);
    if (type === 'tv') {
        setBool(params, 'autonext', playback.autonext);
        setBool(params, 'nextbutton', playback.next_button);
    }

    const primary = playback.sync_accent_color ? accentColourHex() : sanitizeHex(playback.primary_color, '6C63FF');
    params.set('primarycolor', primary);
    params.set('secondarycolor', sanitizeHex(playback.secondary_color, '9F9BFF'));
    params.set('iconcolor', sanitizeHex(playback.icon_color, 'FFFFFF'));

    setBool(params, 'poster', playback.poster);
    setBool(params, 'title', playback.show_title);

    if (playback.icons && playback.icons !== 'default') {
        params.set('icons', playback.icons);
    }

    setBool(params, 'servericon', playback.server_icon);
    setBool(params, 'setting', playback.setting);
    setBool(params, 'pip', playback.pip);
    setBool(params, 'episodelist', playback.episode_list);
    setBool(params, 'chromecast', playback.chromecast);
    setBool(params, 'watchparty', playback.watchparty);
    setBool(params, 'download', playback.download);

    if (playback.font) {
        params.set('font', playback.font);
    }
    params.set('fontcolor', sanitizeHex(playback.font_color, 'FFFFFF'));
    if (playback.font_size) {
        params.set('fontsize', String(playback.font_size));
    }
    if (playback.font_opacity != null) {
        params.set('opacity', String(playback.font_opacity));
    }

    if (playback.logo_url) {
        params.set('logourl', playback.logo_url);
    }

    if (playback.server) {
        params.set('server', playback.server);
    }

    if (playback.resume_from_progress && progressPercent > 0) {
        const seconds = progressToSeconds(progressPercent, runtime, type);
        if (seconds > 0) {
            params.set('progress', String(seconds));
        }
    }

    setBool(params, 'hideprimarycolor', playback.hide_primary_color);
    setBool(params, 'hidesecondarycolor', playback.hide_secondary_color);
    setBool(params, 'hideiconcolor', playback.hide_icon_color);
    setBool(params, 'hideprogresscontrol', playback.hide_progress_control);
    setBool(params, 'hideiconset', playback.hide_icon_set);
    setBool(params, 'hideautonext', playback.hide_autonext);
    setBool(params, 'hideautoplay', playback.hide_autoplay);
    setBool(params, 'hidenextbutton', playback.hide_next_button);
    setBool(params, 'hideposter', playback.hide_poster);
    setBool(params, 'hidetitle', playback.hide_title);
    setBool(params, 'hidechromecast', playback.hide_chromecast);
    setBool(params, 'hideepisodelist', playback.hide_episode_list);
    setBool(params, 'hideservericon', playback.hide_server_icon);
    setBool(params, 'hidepip', playback.hide_pip);

    const query = params.toString();

    return query ? `${path}?${query}` : path;
}

/**
 * VidPhantom embed URLs — https://vidphantom.com/movie/{tmdbId}
 *
 * @see https://vidphantom.com/
 */
export function buildVidphantomUrl({ type, id, season, episode, playback, progressPercent, runtime }) {
    const base = (playback.base_url || 'https://vidphantom.com').replace(/\/$/, '');

    const path =
        type === 'tv'
            ? `${base}/tv/${id}/${season}/${episode}`
            : `${base}/movie/${id}`;

    const params = new URLSearchParams();

    const primary = playback.sync_accent_color ? accentColourHex() : sanitizeHex(playback.primary_color, 'E53535');
    params.set('primaryColor', primary);
    params.set('accentColor', primary);
    params.set('secondaryColor', sanitizeHex(playback.secondary_color, '2A2A2A'));
    params.set('iconColor', sanitizeHex(playback.icon_color, 'FFFFFF'));
    params.set('backdropColor', '000000');

    setBool(params, 'autoplay', playback.autoplay);
    setBool(params, 'poster', playback.poster);

    if (type === 'tv') {
        setBool(params, 'nextbutton', playback.next_button);
    }

    const iconStyle = ['default', 'minimal', 'rounded'].includes(playback.icons)
        ? playback.icons
        : 'default';

    if (iconStyle !== 'default') {
        params.set('icons', iconStyle);
    }

    if (playback.resume_from_progress && progressPercent > 0) {
        const seconds = progressToSeconds(progressPercent, runtime, type);
        if (seconds > 0) {
            params.set('startAt', String(seconds));
        }
    }

    const query = params.toString();

    return query ? `${path}?${query}` : path;
}

function setBool(params, key, value) {
    if (value === undefined || value === null) {
        return;
    }

    params.set(key, value ? 'true' : 'false');
}

function sanitizeHex(value, fallback) {
    const raw = String(value || fallback).replace(/^#/, '').toUpperCase();

    return /^[0-9A-F]{6}$/.test(raw) ? raw : fallback.toUpperCase();
}

function progressToSeconds(percent, runtime, type) {
    const runtimeMinutes = runtime > 0 ? runtime : type === 'tv' ? 45 : 120;

    return Math.floor((Math.min(100, Math.max(0, percent)) / 100) * runtimeMinutes * 60);
}
