import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/';
import AppLayout from './Layouts/AppLayout.vue';
import { applyAppearance } from './lib/appearance.js';
import { clearGuestPayload } from './lib/guestMerge.js';

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    title: (title) => (title ? `${title} · Flexter` : 'Flexter'),
    resolve: async (name) => {
        const loader = pages[`./Pages/${name}.vue`];
        if (!loader) {
            throw new Error(`Unknown page: ${name}`);
        }
        const module = await loader();
        module.default.layout = module.default.layout || AppLayout;

        return module;
    },
    setup({ el, App, props, plugin }) {
        applyAppearance(props.initialPage.props.settings);

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: 'rgb(168 85 247)',
        showSpinner: false,
    },
});

router.on('success', (event) => {
    applyAppearance(event.detail.page.props.settings);

    if (event.detail.page.props.auth?.user) {
        clearGuestPayload();
    }
});

router.on('invalid', () => {
    window.location.reload();
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));
}
