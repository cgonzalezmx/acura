<script setup lang="ts">
import { useDialogRef } from '@/composables/useDialogRef';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { Threshold } from '@/types/analysis';

interface Data {
    analyses: Record<string, any>;
    thresholds: Threshold[];
}

const dialogRef = useDialogRef<Data>();

function disable(analysis: any) {
    if (analysis.canceled) {
        return 'bg-slate-400'
    }
    return 'bg-red-500'
}

function getParameterInfo(parameterId: number, attr: string) {
    return dialogRef.value.data.analyses[parameterId.toString()].parameter[attr];
}
</script>

<template>
    <DataTable
        :value="dialogRef.data.thresholds"
        :rowClass="disable"
    >
        <Column header="Análisis">
            <template #body="{ data }">
                {{ getParameterInfo(data.parameter_id, 'name') }}
            </template>
        </Column>
        <Column header="Unidad">
            <template #body="{ data }">
                {{ getParameterInfo(data.parameter_id, 'unit') }}
            </template>
        </Column>
        <Column field="quantification">
            <template #header>
                <div class="font-semibold text-xs">
                    <div>L.M.C</div>
                    <div>C.M.C</div>
                </div>
            </template>
            <template #body="{ data }">
                {{ dialogRef.data.analyses[data.parameter_id].quantification }}
            </template>
        </Column>
        <Column header="Incert." field="uncertainty"/>
        <Column header="Resultado reportado"/>
    </DataTable>
</template>
