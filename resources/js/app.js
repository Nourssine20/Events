import "./bootstrap";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import { ZiggyVue } from "ziggy";
import VueFeather from "vue-feather";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Toast, {
                position: "top-right",
                timeout: 3000,
            })
            .component(VueFeather.name, VueFeather)
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
