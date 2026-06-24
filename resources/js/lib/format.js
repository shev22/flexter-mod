import { route } from 'ziggy-js';

export function slugify(text) {
    return (
        (text || 'title')
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-') || 'title'
    );
}

export function runtime(minutes) {
    if (!minutes) return null;
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return h ? `${h}h ${m}m` : `${m}m`;
}

/** Build the Inertia detail route for a media summary DTO. */
export function detailRoute(item) {
    const params = { slug: slugify(item.title), id: item.id };
    return item.type === 'tv' ? route('tv.show', params) : route('movie.show', params);
}

export function actorRoute(person) {
    return route('actor.show', { slug: slugify(person.name), id: person.id });
}

/** Extract a 4-digit year from "2024" or "Jul, 2024" style labels. */
export function parseReleaseYear(value) {
    if (value === null || value === undefined || value === '') return null;
    const match = String(value).match(/\b(19|20)\d{2}\b/);
    if (match) return parseInt(match[0], 10);
    const n = parseInt(String(value), 10);
    return Number.isFinite(n) ? n : null;
}

/** Format an ISO date as "Jul, 2024". Returns the input when already formatted. */
export function formatReleaseDate(iso) {
    if (!iso) return '';
    if (/\b(19|20)\d{2}\b/.test(iso) && iso.includes(',')) return iso;
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return iso;
    const month = d.toLocaleDateString('en-US', { month: 'short' });
    return `${month}, ${d.getFullYear()}`;
}

export function mediaTypeLabel(type) {
    if (type === 'tv') return 'Series';
    if (type === 'person') return 'Person';
    return 'Movie';
}

/** Trim long text for compact UI surfaces like search results. */
export function trimText(text, max = 140) {
    const value = (text || '').trim();
    if (!value) return '';
    if (value.length <= max) return value;
    return `${value.slice(0, max).trimEnd()}…`;
}
