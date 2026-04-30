<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface Props {
    analyses: any[];
}

const props = defineProps<Props>();
</script>

<template>
    <div>
        <DataTable>
        </DataTable>
    </div>
</template>

<style scoped>

</style>
