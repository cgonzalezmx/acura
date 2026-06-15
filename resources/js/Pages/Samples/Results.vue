<script setup lang="ts">
import { Sample } from '@/types/sample';
import { format } from 'date-fns';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { useDialog } from 'primevue/usedialog';

interface Props {
    sample: Sample;
}

const props = defineProps<Props>();
const takes = props.sample.takes;
const sampleTimeRange = takes.map((t) => t.timestamp);
const sampleDate = () => {
    if (!props.sample.sampled_by) {
        return 'Cliente';
    }

    return format(sampleTimeRange.at(0), 'dd/MM/yyyy');
}
const samplingLapse = () => {
    const range = [];
    range.push(sampleTimeRange.at(0));

    if (sampleTimeRange.length > 1) {
        range.push(sampleTimeRange.at(-1));
    }

    return range.map((t) => format(t, 'HH:mm'));
}
const reports = listReports();
const dialog = useDialog();
const hasManyReports = props.sample.reports_count > 1;

function listReports() {
    const reports = [];
    for (let i = 0; i < props.sample.reports_count; i++) {
        reports.push(String.fromCharCode(65 + i));
    }

    return reports;
}
</script>

<template>
    <table>
        <thead>
            <tr>
                <th class="header">Clave de muestra</th>
                <th class="header"></th>
                <th class="header">Fecha</th>
                <th class="header">Hora</th>
                <th class="header">Observaciones de cotización</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="2" class="cell">{{ sample.identifier }}</td>
                <td class="cell font-bold">Muestreo</td>
                <td class="cell">{{ sampleDate() }}</td>
                <td class="cell">{{ samplingLapse().join(' - ') }}</td>
                <td rowspan="2" class="cell">{{ sample.quote.notes }}</td>
            </tr>
            <tr>
                <td class="cell font-bold">Recepción</td>
                <td class="cell">{{ format(sample.reception_date, 'dd/MM/yyyy') }}</td>
                <td class="cell">{{ format(sample.reception_date, 'HH:mm') }}</td>
            </tr>
        </tbody>
    </table>
    <div v-if="hasManyReports" class="flex justify-center gap-2">
        <Button v-for="letter in reports" class="font-mono">
            Informe {{ letter }}
        </Button>
    </div>
    <DataTable :value="sample.analyses">
        <Column header="Análisis" field="parameter.name"/>
        <Column header="Resultado calculado"/>
        <Column header="Unidad" field="parameter.unit"/>
        <Column header="Limite de cuantificación" field="parameter.quantification"/>
        <Column header="Incertidumbre" field="parameter.uncertainty"/>
        <Column header="Resultado reportado"/>
        <template v-if="!hasManyReports">
            <Column header="LMP">
                <template #body="{ data }">
                    {{ data.thresholds[0].max }}
                </template>
            </Column>
            <Column header="Dictamen">
                <template #body="{ data }">
                    {{ data.thresholds.passed }}
                </template>
            </Column>
        </template>
    </DataTable>
</template>
