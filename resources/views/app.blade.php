<!DOCTYPE html>
<html lang="en" data-accent="aurora">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#9333ea" />
    <script>
        (function () {
            try {
                var stored = JSON.parse(localStorage.getItem('flexter.appearance') || '{}');
                var root = document.documentElement;
                var theme = stored.theme || 'dark';
                var accent = stored.accent || 'aurora';
                var density = stored.density || 'comfortable';

                root.setAttribute('data-accent', accent);
                root.setAttribute('data-density', density);

                if (stored.high_contrast) {
                    root.setAttribute('data-high-contrast', 'true');
                }

                if (stored.reduce_motion) {
                    root.setAttribute('data-reduce-motion', 'true');
                }

                var prefersDark = window.matchMedia
                    && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var isDark = theme === 'dark' || (theme === 'system' && prefersDark);
                root.classList.toggle('dark', isDark);
            } catch (e) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="manifest" href="/manifest.json" />
    <link rel="icon" type="image/svg+xml" />
    <link rel="apple-touch-icon" />
    <script>
        (function () {
            try {
                var accent = document.documentElement.getAttribute('data-accent') || 'aurora';
                var accents = {
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
                var stops = accents[accent] || accents.aurora;
                var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none"><defs><linearGradient id="g" x1="4" y1="2" x2="28" y2="30" gradientUnits="userSpaceOnUse"><stop stop-color="' + stops[0] + '"/><stop offset="1" stop-color="' + stops[1] + '"/></linearGradient></defs><rect width="32" height="32" rx="8" fill="url(#g)"/><text x="16" y="24" text-anchor="middle" font-family="system-ui,sans-serif" font-size="25" font-weight="800" fill="#ffffff">F</text></svg>';
                var iconHref = 'data:image/svg+xml,' + encodeURIComponent(svg);
                document.querySelector('link[rel="icon"]').href = iconHref;
                document.querySelector('link[rel="apple-touch-icon"]').href = iconHref;
                document.querySelector('meta[name="theme-color"]').content = stops[0];
            } catch (e) {}
        })();
    </script>
    <title inertia>Flexter</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://image.tmdb.org">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">

    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
