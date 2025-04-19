import './bootstrap';
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/";
import {Ziggy} from "./ziggy.js";
import {setThemeOnLoad} from "./theme.js";
import Main from "./Layouts/Main.vue";
import Home from "./Layouts/Home.vue";


createInertiaApp({
    title: (title) => `Flexter ${title}`,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page = pages[`./Pages/${name}.vue`]
        page.default.layout = page.default.layout || Main

        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el)
    },
    progress:{
        color: "green",
        includeCSS: true,
        showSpinner: false
    }
}).then((r) => {});

setThemeOnLoad()
