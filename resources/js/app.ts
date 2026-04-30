import './bootstrap';
import '../css/app.css';
import '../css/base.css';
import 'primeicons/primeicons.css';
import '@fortawesome/fontawesome-free/css/all.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import Tooltip from 'primevue/tooltip';
import PrimeVue from 'primevue/config';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Aura from '@primeuix/themes/aura';
import DialogService from 'primevue/dialogservice';
import { VueQueryPlugin } from '@tanstack/vue-query';

const pinia = createPinia();

createInertiaApp({
    withApp(app) {
        app
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
    }
});
