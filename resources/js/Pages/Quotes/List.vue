<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Menubar from 'primevue/menubar';
import { Head, router } from '@inertiajs/vue3';
import { MenuItem } from 'primevue/menuitem';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { defineAsyncComponent, ref } from 'vue';
import vCurrency from '@/Directives/vCurrency';
import PopupMenu from '@/Components/PopupMenu.vue';
import { useDialog } from 'primevue/usedialog';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import ConfirmationDialog from './Partials/ConfirmationDialog.vue';
import { timestamp } from '@/utils/formatters';
import RangeDatePicker from '@/Components/RangeDatePicker.vue';
import Toolbar from 'primevue/toolbar';
import { format, startOfMonth } from 'date-fns';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';

interface Props {
    quotes: any[];
}

defineOptions({ layout: HomeLayout });
const { quotes } = defineProps<Props>();
const SamplingSites = defineAsyncComponent(() => import('./Partials/SamplingSites.vue'));
const dialog = useDialog();
const toast = useToast();

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
        icon: 'fa-solid fa-check'
    },
];

const filters = ref({});
const selection = ref();
const confirmationDialogVisible = ref(false);
const date = new Date();
const firstOfMonth = startOfMonth(date);
const form = useForm({
    from: firstOfMonth,
    until: date
});

function search() {
    form
        .transform((data) => {
            const dateFormat = 'yyyy-MM-dd';
            return {
                from: format(data.from, dateFormat),
                until: format(data.until, dateFormat)
            }
        })
        .get(route('quotes.index'), {
        only: ['quotes'],
        preserveState: true
    });
}

function copy() {
    if (!selection.value) {
        toast.add({
            detail: 'Debe seleccionar una cotización.',
            severity: 'warn',
            life: 3000
        });
        return;
    }

    const dialogInstance = dialog.open(ConfirmationDialog, {
        props: {
            modal: true,
            draggable: false,
            header: 'Copiar cotización',
        },
        data: {
            quoteIdentifier: selection.value.identifier
        },
        emits: {
            onConfirm() {
                router.post(route('quotes.copy', selection.value.id), undefined, {
                    onSuccess() {
                        toast.add({
                            detail: `Se ha copiado ${selection.value.identifier} con éxito.`,
                            severity: 'success',
                            life: 3000
                        });
                        dialogInstance.close();
                    }
                });
            }
        }
    })

}

function authorize() {

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
    <DataTable
        v-model:filters="filters"
        v-model:selection="selection"
        :value="quotes"
        selection-mode="single"
        data-key="id"
        pt:root:class="flex flex-col overflow-hidden w-full">
        <template #header>
            <Toolbar>
                <template #start>
                    <RangeDatePicker v-model:start="form.from" v-model:end="form.until"/>
                    <Button label="Buscar" @click="search"/>
                </template>
            </Toolbar>
            <Menubar :model="menuItems">
                <template #end>
                    <IconField>
                        <InputIcon class="fa-solid fa-magnifying-glass"/>
                        <InputText/>
                    </IconField>
                </template>
            </Menubar>
        </template>
        <Column>
            <template #body="{ data }">
                <PopupMenu
                    outlined
                    severity="secondary"
                    :model="[
                        {
                            label: 'Editar',
                            icon: 'fa-solid fa-file-pen',
                            url: route('quotes.edit', data),
                            target: '_blank'
                        },
                        {
                            label: 'PDF',
                            icon: 'fa-solid fa-file-pdf',
                            url: route('quotes.show', data.id),
                            target: '_blank'
                        },
                        {
                            label: 'Puntos de muestreo',
                            icon: 'fa-solid fa-location-dot',
                            command: () => showSampleSites(data)
                        }
                    ]"/>
            </template>
        </Column>
        <Column>
        </Column>
        <Column header="Identificador" field="identifier"/>
        <Column header="Fecha de registro">
            <template #body="{ data }">
                {{ timestamp(data.created_at) }}
            </template>
        </Column>
        <Column header="Costo bruto">
            <template #body="{ data }">
                <span v-currency="data.gross_cost"></span>
            </template>
        </Column>
        <Column header="Costo neto">
            <template #body="{ data }">
                <span v-currency="data.net_cost"></span>
            </template>
        </Column>
        <Column header="Cliente" field="client"/>
        <Column header="Contacto" field="contact_name"/>
        <Column header="Teléfono" field="contact_phone"/>
        <Column header="Email" field="contact_email"/>
    </DataTable>
</template>
