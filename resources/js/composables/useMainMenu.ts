import { usePermission } from './usePermission';
import { usePage } from '@inertiajs/vue3';
import {
    faHouse,
    faFileInvoiceDollar,
    faFolderTree,
    faTableList,
    faBook,
    IconDefinition,
    faUsers,
    faMapLocationDot,
    faBoxesStacked,
    faClipboardList,
    faAtom,
    faChartLine,
    faSun,
    faBacteria,
    faFlaskVial,
    faFileInvoice,
    faAddressBook,
} from '@fortawesome/free-solid-svg-icons';

interface MenuItem {
    label: string;
    faIcon: IconDefinition;
    route: [string] | [string, Record<string, any> | any];
}

export function useMainMenu() {
    const { permissions } = usePermission();
    let items: Record<string, MenuItem> = {
        'quotes.view': {
            label: 'Cotizaciones',
            faIcon: faFileInvoiceDollar,
            route: ['quotes.index']
        },
        'sampling_formats.view': {
            label: 'Muestreo',
            faIcon: faMapLocationDot,
            route: ['sampling-formats.index']
        },
        'samples.view': {
            label: 'Aprobación de muestras',
            faIcon: faBoxesStacked,
            route: ['samples.index']
        },
        'work_orders.view': {
            label: 'Orden de trabajo',
            faIcon: faClipboardList,
            route: ['work-orders.index']
        },
        'anaylsys.view.a1': {
            label: 'Área 1',
            faIcon: faFlaskVial,
            route: ['batches.index', 'A1']
        },
        'anaylsys.view.a2': {
            label: 'Área 2',
            faIcon: faFlaskVial,
            route: ['batches.index', 'A2']
        },
        'reports.view': {
            label: 'Informes',
            faIcon: faFileInvoice,
            route: ['reports.index']
        },
        'parameters.view': {
            label: 'Parámetros',
            faIcon: faTableList,
            route: ['parameters.index']
        },
        'regulations.view': {
            label: 'Regulaciones',
            faIcon: faFolderTree,
            route: ['regulations.tree']
        },
        'catalog.manage': {
            label: 'Catálogo',
            faIcon: faBook,
            route: ['catalog']
        },
        'users.manage': {
            label: 'Usuarios',
            faIcon: faUsers,
            route: ['users.index']
        },
        'client.manage': {
            label: 'Clientes',
            faIcon: faAddressBook,
            route: ['clients.index']
        }
    };

    const menuItems: MenuItem[] = [
        {
            label: 'Inicio',
            faIcon: faHouse,
            route: ['home']
        }
    ];

    if (usePage().props.auth.user?.is_admin) {
        return menuItems.concat(Object.values(items));
    }

    permissions?.forEach((p: any) => {
        const item = items[p];

        if (item) {
            menuItems.push(item);
        }
    });
    return menuItems;
}
