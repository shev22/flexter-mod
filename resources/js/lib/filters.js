import { parseReleaseYear } from './format.js';

/** Rating buckets (whole-star floor) for multi-select filters. */
export const ratingOptions = [
    { value: 9, label: '9 ★' },
    { value: 8, label: '8 ★' },
    { value: 7, label: '7 ★' },
    { value: 6, label: '6 ★' },
    { value: 5, label: '5 ★' },
];

export function toList(value) {
    if (Array.isArray(value)) return value.filter((v) => v !== null && v !== '');
    if (value === null || value === undefined || value === '') return [];
    return [value];
}

export function floorRating(value) {
    const n = Number(value);
    return Number.isFinite(n) ? Math.floor(n) : null;
}

export function yearOptionsFromItems(items, fromYear = 1970) {
    const now = new Date().getFullYear();
    const years = new Set(
        items
            .map((item) => parseReleaseYear(item.year))
            .filter((y) => y !== null && y >= fromYear && y <= now),
    );

    return [...years]
        .sort((a, b) => b - a)
        .map((y) => ({ value: y, label: String(y) }));
}

export function filterMediaItems(items, { search = '', years = [], ratings = [] } = {}) {
    let result = items;

    const q = search.trim().toLowerCase();
    if (q) {
        result = result.filter((item) => (item.title || '').toLowerCase().includes(q));
    }

    if (years.length) {
        const yearSet = new Set(years.map(Number));
        result = result.filter((item) => yearSet.has(parseReleaseYear(item.year)));
    }

    if (ratings.length) {
        const ratingSet = new Set(ratings.map(Number));
        result = result.filter((item) => {
            const floor = floorRating(item.rating);
            return floor !== null && ratingSet.has(floor);
        });
    }

    return result;
}
