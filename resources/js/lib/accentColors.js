/** Accent gradient stops — keep in sync with `app.css` `[data-accent]` tokens. */
export const ACCENT_GRADIENTS = {
    aurora: ['#a855f7', '#d946ef'],
    sunset: ['#f97316', '#f43f5e'],
    emerald: ['#10b981', '#14b8a6'],
    crimson: ['#f43f5e', '#be185d'],
    ocean: ['#38bdf8', '#6366f1'],
    gold: ['#f59e0b', '#eab308'],
    cobalt: ['#3b82f6', '#1d4ed8'],
    midnight: ['#6366f1', '#312e81'],
    forest: ['#166534', '#14532d'],
    wine: ['#881337', '#4c0519'],
    plum: ['#581c87', '#3b0764'],
    navy: ['#1e3a8a', '#172554'],
    slate: ['#334155', '#1e293b'],
    rust: ['#9a3412', '#7c2d12'],
    teal: ['#115e59', '#0f766e'],
    bronze: ['#92400e', '#78350f'],
    charcoal: ['#374151', '#1f2937'],
    moss: ['#365314', '#3f6212'],
    lavender: ['#a78bfa', '#818cf8'],
    mint: ['#34d399', '#2dd4bf'],
    amber: ['#fbbf24', '#f97316'],
    peach: ['#fb7185', '#fda4af'],
};

export function faviconDataUrl(accent = 'aurora') {
    const [from, to] = ACCENT_GRADIENTS[accent] ?? ACCENT_GRADIENTS.aurora;
    const svg = [
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none">',
        '<defs><linearGradient id="g" x1="4" y1="2" x2="28" y2="30" gradientUnits="userSpaceOnUse">',
        `<stop stop-color="${from}"/><stop offset="1" stop-color="${to}"/>`,
        '</linearGradient></defs>',
        '<rect width="32" height="32" rx="8" fill="url(#g)"/>',
        '<text x="16" y="24" text-anchor="middle" font-family="system-ui,sans-serif" font-size="25" font-weight="800" fill="#ffffff">F</text>',
        '</svg>',
    ].join('');

    return `data:image/svg+xml,${encodeURIComponent(svg)}`;
}

export function applyFavicon(accent = 'aurora') {
    const href = faviconDataUrl(accent);
    const [primary] = ACCENT_GRADIENTS[accent] ?? ACCENT_GRADIENTS.aurora;

    for (const rel of ['icon', 'apple-touch-icon']) {
        let link = document.querySelector(`link[rel="${rel}"]`);

        if (!link) {
            link = document.createElement('link');
            link.rel = rel;
            document.head.appendChild(link);
        }

        link.href = href;

        if (rel === 'icon') {
            link.type = 'image/svg+xml';
        }
    }

    const themeColor = document.querySelector('meta[name="theme-color"]');

    if (themeColor) {
        themeColor.content = primary;
    }
}
