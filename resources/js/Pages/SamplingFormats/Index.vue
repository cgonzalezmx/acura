<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Button from 'primevue/button';
import { Head } from '@inertiajs/vue3';

interface Props {
    samplingFormats: any[];
}

const props = defineProps<Props>();
defineOptions({ layout: HomeLayout });
</script>

<template>
    <Head title="Formatos de muestreo"/>
    <DataTable :value="samplingFormats">
        <Column>
            <template #body="{ data }">
                <Button label="imprimir" as="a" :href="route('sampling-formats.show', `${data.identifier}`)" target="_blank"/>
            </template>
        </Column>
        <Column header="Cotización" field="quote_identifier"/>
        <Column header="Código" field="identifier"/>
        <Column header="Partida" field="entry_index"/>
        <Column header="Fecha de registro" field="created_at"/>
        <Column header="Cliente" field="client_name"/>
        <Column header="Contacto" field="contact_name"/>
        <Column header="Teléfono" field="contact_phone"/>
        <Column header="Email" field="contact_email"/>
    </DataTable>
</template>
