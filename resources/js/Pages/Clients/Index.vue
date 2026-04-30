<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import HomeLayout from '@/Layouts/HomeLayout.vue';

interface Props {
    clients: any[];
}

defineOptions({ layout: HomeLayout });
const props = defineProps<Props>();

</script>

<template>
    <div class="flex flex-col h-full overflow-hidden">
        <DataTable
            :value="clients"
            paginator
            scrollable
            :rows="15"
            pt:root:class="flex flex-col overflow-hidden"
        >
            <Column header="No. cliente" field="id"/>
            <Column header="Nombre" field="name"/>
            <Column header="Estatus" field="active">
                <template #body="{data}">
                    {{ data.active ? 'activo' : 'baja' }}
                </template>
            </Column>
            <Column header="Tipo de industria" field="industry_sector"/>
            <Column header="Calle" fiedl="street"/>
            <Column header="No. exterior" field="external_number"/>
            <Column header="No. interior" field="internal_number"/>
            <Column header="Colonia" field="neighborhood"/>
            <Column header="Código postal" field="zip_code"/>
            <Column header="Ciudad" field="city"/>
            <Column header="Estado" field="state"/>
        </DataTable>
    </div>
</template>

<style scoped>

</style>
