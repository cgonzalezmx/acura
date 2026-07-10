<script setup lang="ts">
import Column from 'primevue/column';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Button from 'primevue/button';
import { Head } from '@inertiajs/vue3';
import IndexTable from '@/Components/IndexTable.vue';
import { MenuItem } from 'primevue/menuitem';
import { useTemplateRef } from 'vue';

interface Props {
    items: any[];
}

const props = defineProps<Props>();
defineOptions({ layout: HomeLayout });
const menuItems: MenuItem[] = [
    {
        label: 'Borrar',
        icon: 'fa-solid fa-trash',
        command: trash
    }
];
const indexTable = useTemplateRef<InstanceType<typeof IndexTable>>('indexTable');

function trash() {
    const selection = indexTable.value?.selection;
    indexTable.value?.openConfirmationDialog({
        method: 'delete',
        header: 'Eliminar formato',
        body: [`¿Eliminar ${selection.identifier}?`],
        successMessage: `Se eliminó ${selection.identifier}`,
        route: route('sampling-formats.destroy', selection.id)
    });
}
</script>

<template>
    <Head title="Formatos de muestreo"/>
    <IndexTable
        :value="items"
        :menu-items
        :globalFilters="['identifier', 'quote_identifier']"
        route="sampling-formats"
        ref="indexTable">
        <Column selection-mode="single"/>
        <Column>
            <template #body="{ data }">
                    <Button icon="fa-solid fa-file-pdf" as="a" :href="route('sampling-formats.show', `${data.identifier}`)" target="_blank"/>
            </template>
        </Column>
        <Column header="Cotización" field="quote_identifier"/>
        <Column header="Código" field="identifier"/>
        <Column header="Partida" field="entry_index"/>
        <Column header="Fecha de registro" field="created_at"/>
        <Column header="Cliente" field="client_name"/>
    </IndexTable>
</template>
