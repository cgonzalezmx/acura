<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface Props {
    activeRoles: any;
}

const props = defineProps<Props>();
</script>

<template>
    <DataTable :value="activeRoles">
        <Column header="Nombre" field="label"/>
    </DataTable>
</template>