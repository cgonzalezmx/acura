<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Column from 'primevue/column';
import { Head } from '@inertiajs/vue3';
import { MenuItem } from 'primevue/menuitem';
import { defineAsyncComponent, useTemplateRef } from 'vue';
import vCurrency from '@/Directives/vCurrency';
import { useDialog } from 'primevue/usedialog';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { timestamp } from '@/utils/formatters';
import Button from 'primevue/button';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCircleCheck } from '@fortawesome/free-solid-svg-icons';
import IndexTable from '@/Components/IndexTable.vue';

interface Props {
    quotes: any[];
}

defineOptions({ layout: HomeLayout });
const { quotes } = defineProps<Props>();
const SamplingSites = defineAsyncComponent(() => import('./Partials/SamplingSites.vue'));
const dialog = useDialog();
const toast = useToast();
const indexTable = useTemplateRef<InstanceType<typeof IndexTable>>('indexTable');

const menuItems: MenuItem[] = [
    {
        label: 'Nueva',
        icon: 'fa-solid fa-plus',
        url: route('quotes.create'),
        target: '_blank'
    },
    {
        label: 'Copiar',
        icon: 'fa-solid fa-copy',
        command: copy
    },
    {
        label: 'autorizar',
        icon: 'fa-solid fa-check',
        command: authorize
    },
    {
        label: 'Eliminar',
        icon: 'fa-solid fa-trash',
        command: trash
    }
];

function copy() {
    if (!indexTable.value?.checkSelection()) {
        return;
    }

    const selection = indexTable.value.selection;
    indexTable.value?.openConfirmationDialog({
        header: 'Copiar cotización',
        body: [`¿Desea hacer una copia de ${selection.identifier}?`],
        successMessage: `Se ha copiado ${selection.identifier} con éxito.`,
        route: route('quotes.copy', selection.id),
    });
}

function authorize() {
    if (!indexTable.value?.checkSelection()) {
        return;
    }

    if (indexTable.value.selection.authorized) {
        toast.add({
            severity: 'info',
            detail: 'Esta cotización ya ha sido autorizada',
            life: 3000
        });
        return;
    }

    const selection = indexTable.value.selection;
    indexTable.value.openConfirmationDialog({
        header: 'Autorizar cotización',
        body: [
            `¿Autorizar ${selection.identifier}?`,
            'Una vez autorizada no se podrán realizar cambios.'
        ],
        successMessage: `Se ha autorizado ${selection.identifier} con éxito.`,
        route: route('quotes.authorize', selection.id),
    });
}

function trash() {
    const selection = indexTable.value?.selection;
    indexTable.value?.openConfirmationDialog({
        method: 'delete',
        header: 'Eliminar cotización',
        body: [`¿Eliminar ${selection.identifier}?`],
        successMessage: `Se eliminó ${selection.identifier}`,
        route: route('quotes.destroy', selection.id)
    });
}

async function showSampleSites(quote: any) {
    const { data } = await axios.get(route('quotes.entries', quote.id));
    dialog.open(SamplingSites, {
        props: {
            header: `Partidas ${quote.identifier}`,
            draggable: false,
            modal: true
        },
        data: {
            entries: data
        }
    });
}
</script>

<template>
    <Head title="Cotizaciones"/>
    <h2 class="text-3xl font-semibold">Cotizaciones</h2>
    <IndexTable :value="quotes" :menu-items :global-filters="['identifier', 'client']" ref="indexTable">
        <Column selection-mode="single"/>
        <Column>
            <template #body="{ data }">
                <div class="grid grid-cols-3">
                    <div>
                        <Button v-if="!data.authorized"
                            icon="fa-solid fa-file-pen"
                            as="a"
                            :href="route('quotes.edit', data.id)"
                            target="_blank"
                        />
                    </div>
                    <div>
                        <Button v-if="data.authorized" icon="fa-solid fa-location-dot" @click="showSampleSites(data)"/>
                    </div>
                    <div>
                        <Button icon="fa-solid fa-file-pdf"
                            as="a"
                            :href="route('quotes.show', data.id)"
                            target="_blank"
                        />
                    </div>
                </div>
            </template>
        </Column>
        <Column header="Identificador" field="identifier"/>
        <Column header="Autorizada">
            <template #body="{ data }">
                <div v-if="data.authorized" class="flex justify-center">
                    <FontAwesomeIcon :icon="faCircleCheck" size="xl" class="text-emerald-500"/>
                </div>
            </template>
        </Column>
        <Column header="Fecha de registro">
            <template #body="{ data }">
                {{ timestamp(data.created_at) }}
            </template>
        </Column>
        <Column header="Costo">
            <template #body="{ data }">
                <span v-currency="data.net_cost"></span>
            </template>
        </Column>
        <Column header="Cliente" field="client"/>
        <Column header="Contacto" field="contact_name"/>
        <Column header="Teléfono" field="contact_phone"/>
    </IndexTable>
</template>
