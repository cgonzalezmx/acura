<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Sample } from '@/types/sample';

interface Props {
    sample: Sample;
}

const props = defineProps<Props>();
</script>

<template>
    <div>
    </div>
    <div>
        <DataTable>
        </DataTable>
    </div>
</template>
