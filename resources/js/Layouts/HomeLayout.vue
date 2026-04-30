<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import Menu from 'primevue/menu';
import DynamicDialog from 'primevue/dynamicdialog';
import Toast from 'primevue/toast';
import {
    IconDefinition,
    faArrowRightFromBracket
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useMainMenu } from '@/composables/useMainMenu';

interface MenuItem {
    label: string;
    faIcon: IconDefinition;
    route: string[];
    method?: string;
    expandable?: boolean;
}

const user = usePage().props.auth.user.name;
const menuItems = useMainMenu();

const active = (item: MenuItem) => {
    const [name, params] = item.route;

    if (route().current(name, params)) {
        return 'bg-primary-500';
    }

    return 'hover:bg-surface-600';
}

const url = (itemRoute: string[]) => {
    return itemRoute.length > 1 ? route(itemRoute[0], itemRoute[1]) : route(itemRoute[0]);
}
</script>

<template>
    <Toast />
    <DynamicDialog />
    <div class="flex h-screen">
        <Menu :model="menuItems" :unstyled="true" :pt="{
            item: ({ context }) => ({ class: active(context.item) }),
            root: {
                class: 'bg-surface-700 text-surface-200 w-64'
            }
        }">
            <template #start>
                <div class="text-xl font-semibold text-center py-4 border-b border-b-surface-500">Laboratorio</div>
            </template>
            <template #item="{ item }">
                <Link v-if="item.route" :href="url(item.route)" class="block px-4 py-2">
                    <FontAwesomeIcon :icon="item.faIcon" fixed-width />
                    {{ item.label }}
                </Link>
            </template>
            <template #end>
                <Link :href="route('logout')" method="post" as="button" class="block px-4 py-2 cursor-pointer w-full text-left">
                    <FontAwesomeIcon :icon="faArrowRightFromBracket" fixed-width/>
                    Cerrar sesión
                </Link>
            </template>
        </Menu>
        <div class="flex flex-col flex-1 bg-surface-100 overflow-auto">
            <div class="p-4 border-b border-b-surface-300 bg-surface-0">
                Bienvenido(a): <span class="text-xl font-semibold">{{ user }}</span>
            </div>
            <slot />
        </div>
    </div>
</template>
