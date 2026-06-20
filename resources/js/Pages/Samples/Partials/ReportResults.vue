<script setup lang="ts">
import { useDialogRef } from '@/composables/useDialogRef';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Threshold } from '@/types/analysis';

interface Data {
    parameterMap: Record<number, any>;
    thresholds: Threshold[];
}

const dialogRef = useDialogRef<Data>();
</script>

<template>
    <DataTable :value="dialogRef.data.thresholds">
        <Column header="Análisis">
            <template #body="{ data }">
                {{ dialogRef.data.parameterMap[data.parameter_id].name }}
            </template>
        </Column>
        <Column header="Unidad">
            <template #body="{ data }">
                {{ dialogRef.data.parameterMap[data.parameter_id].unit }}
            </template>
        </Column>
        <Column field="quantification">
            <template #header>
                <div class="font-semibold text-xs">
                    <div>L.M.C</div>
                    <div>C.M.C</div>
                </div>
                <template #body="{ data }">
                    {{ dialogRef.data.parameterMap[data.parameter_id].quantification }}
                </template>
            </template>
        </Column>
        <Column header="Incert." field="uncertainty"/>
    </DataTable>
</template>
