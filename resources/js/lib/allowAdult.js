/**
 * Client-side adult filter — mirrors AdultContent on the server.
 */
export function allowsAdultContent(item, allowAdult = false) {
    if (!item || allowAdult || item.type === 'person') {
        return true;
    }

    return !item.adult;
}

export function filterAdultContent(items, allowAdult = false) {
    if (!Array.isArray(items) || allowAdult) {
        return items ?? [];
    }

    return items.filter((item) => allowsAdultContent(item, allowAdult));
}
