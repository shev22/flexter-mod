/**
 * Applies the user's theme + accent preferences to the document root. Accent is
 * driven by the `data-accent` attribute (see app.css) and theme toggles the
 * `dark` class. Falls back to the OS preference when theme is "system".
 */
import { applyFavicon } from './accentColors.js';

const STORAGE_KEY = 'flexter.appearance';

let systemThemeListener;

function persistAppearance(settings) {
    try {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({
                theme: settings?.theme ?? 'dark',
                accent: settings?.accent ?? 'aurora',
                density: settings?.density ?? 'comfortable',
                high_contrast: !!settings?.high_contrast,
                reduce_motion: !!settings?.reduce_motion,
            }),
        );
    } catch {
        // localStorage may be unavailable in private browsing.
    }
}

export function applyAppearance(settings) {
    const root = document.documentElement;
    const theme = settings?.theme ?? 'dark';
    const accent = settings?.accent ?? 'aurora';
    const density = settings?.density ?? 'comfortable';

    root.setAttribute('data-accent', accent);
    root.setAttribute('data-density', density);

    if (settings?.high_contrast) {
        root.setAttribute('data-high-contrast', 'true');
    } else {
        root.removeAttribute('data-high-contrast');
    }

    if (settings?.reduce_motion) {
        root.setAttribute('data-reduce-motion', 'true');
    } else {
        root.removeAttribute('data-reduce-motion');
    }

    const applyThemeClass = () => {
        const prefersDark =
            window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = theme === 'dark' || (theme === 'system' && prefersDark);
        root.classList.toggle('dark', isDark);
    };

    applyThemeClass();
    persistAppearance(settings);
    applyFavicon(accent);

    if (systemThemeListener) {
        systemThemeListener.removeEventListener('change', applyThemeClass);
        systemThemeListener = null;
    }

    if (theme === 'system' && window.matchMedia) {
        systemThemeListener = window.matchMedia('(prefers-color-scheme: dark)');
        systemThemeListener.addEventListener('change', applyThemeClass);
    }
}
