import './bootstrap';
import '../css/app.css';
import '../css/base.css';
import 'primeicons/primeicons.css';
import '@fortawesome/fontawesome-free/css/all.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp, usePage } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Tooltip from 'primevue/tooltip';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Aura from '@primeuix/themes/aura';
import DialogService from 'primevue/dialogservice';
import { createPinia } from 'pinia';
import { VueQueryPlugin } from '@tanstack/vue-query';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent([`./Pages/${name}.vue`, `./Pages/${name}.ts`], import.meta.glob<DefineComponent>(['./Pages/**/*.vue', './Pages/**/*.ts'])),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        cssLayer: {
                            name: 'primevue',
                            order: 'theme, base, primevue'
                        }
                    }
                },
                locale: {
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene.', 'Feb.', 'Mar.', 'Abr.', 'May.', 'Jun.', 'Jul.', 'Ago.', 'Sep.', 'Oct.', 'Nov.', 'Dic.'],
                    dayNamesMin: ['Do.', 'Lu.', 'Ma.', 'Mi.', 'Ju.', 'Vi.', 'Sá.'],
                    clear: 'Limpiar',
                    apply: 'Aplicar',
                    emptyMessage: 'Sin opciones.'
                }
            })
            .use(ConfirmationService)
            .use(ToastService)
            .use(DialogService)
            .use(VueQueryPlugin)
            .directive('tooltip', Tooltip);

            app.config.globalProperties.$can = (permissions: string[]) => usePage().props.auth.user.permissions.contains(permissions)
            app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
