<script setup lang="ts">
import { Sample } from '@/types/sample';
import { format } from 'date-fns';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { useDialog } from 'primevue/usedialog';
import { defineAsyncComponent } from 'vue';
import DynamicDialog from 'primevue/dynamicdialog';
import { Analysis } from '@/types/analysis';

interface Props {
    sample: Sample & { analyses: Analysis[] };
}

const ReportResults = defineAsyncComponent(() => {
    return import('./Partials/ReportResults.vue');
});
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
const analyses = Object.values(props.sample.analyses);

function listReports() {
    const reports = [];
    for (let i = 0; i < props.sample.reports_count; i++) {
        reports.push(String.fromCharCode(65 + i));
    }

    return reports;
}

function openReport(letter: string) {
    const thresholds = props.sample.thresholds.filter((thres: any) => thres.letter === letter);
    dialog.open(ReportResults, {
        props: {
            modal: true,
            draggable: false,
        },
        data: {
            thresholds,
            analyses: props.sample.analyses,
        }
    });
}

function showAnalyzedAt(date?: string) {
    if (date) {
        return format(new Date(date), 'dd/MM/yyyy');
    }
}
</script>

<template>
    <table>
        <thead>
            <tr>
                <th class="header">Clave de muestra</th>
                <th class="header">Cliente</th>
                <th class="header">Punto de muestreo</th>
                <th class="header"></th>
                <th class="header">Fecha</th>
                <th class="header">Hora</th>
                <th class="header">Observaciones de cotización</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="2" class="cell">{{ sample.identifier }}</td>
                <td rowspan="2" class="cell">{{ sample.quote.client.name }}</td>
                <td rowspan="2" class="cell">{{ sample.sampling_point }}</td>
                <td class="cell font-semibold">Muestreo</td>
                <td class="cell">{{ sampleDate() }}</td>
                <td class="cell">{{ samplingLapse().join(' - ') }}</td>
                <td rowspan="2" class="cell">{{ sample.quote.notes }}</td>
            </tr>
            <tr>
                <td class="cell font-bold">Recepción</td>
                <td class="cell">{{ format(sample?.reception_date, 'dd/MM/yyyy') }}</td>
                <td class="cell">{{ format(sample?.reception_date, 'HH:mm') }}</td>
            </tr>
        </tbody>
    </table>
    <div class="flex justify-center gap-2">
        <Button v-for="letter in reports" @click="openReport(letter)" class="font-mono">
            Informe {{ letter }}
        </Button>
    </div>
    <DataTable :value="analyses" class="font-mono">
        <Column header="Análisis" field="parameter.name"/>
        <Column header="Resultado calculado" field="result"/>
        <Column header="Unidad" field="parameter.unit"/>
        <Column header="Limite de cuantificación" field="parameter.quantification"/>
        <Column header="Coeficiente de incertidumbre" field="parameter.uncertainty"/>
        <Column header="Resultado reportado" field="reported_result"/>
        <Column header="LMP" field="smallest_max_threshold"/>
        <Column header="Fecha de análisis">
            <template #body="{ data }">
                {{ showAnalyzedAt(data.analyzed_at) }}
            </template>
        </Column>
        <Column header="Analista" field="analyzed_by.alias"/>
        <Column header="Bitácora" field="log"/>
        <Column header="Método" field="method"/>
    </DataTable>
    <DynamicDialog/>
</template>
