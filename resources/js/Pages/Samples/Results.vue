<script setup lang="ts">
import { Sample } from '@/types/sample';
import { format } from 'date-fns';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { useDialog } from 'primevue/usedialog';
import { defineAsyncComponent, onMounted } from 'vue';
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
const thresholdMap = mapThresholds();
const parameterMap: Record<string, any> = {};

function listReports() {
    const reports = [];
    for (let i = 0; i < props.sample.reports_count; i++) {
        reports.push(String.fromCharCode(65 + i));
    }

    return reports;
}

function mapParameters() {
    props.sample.analyses.forEach((analysis) => {
        parameterMap[analysis.parameter_id] = analysis.parameter;
    });
}

function mapThresholds() {
    const map: Record<string, any> = {};
    props.sample.analyses.forEach((analysis: any) => {
        analysis.thresholds.forEach((threshold: any) => {
            const report = map[threshold.letter];

            if (typeof report !== 'undefined') {
                report.push(threshold);
                return;
            }

            map[threshold.letter] = [threshold];
        });
    });
    return map;
}

function openReport(letter: string) {
    dialog.open(ReportResults, {
        props: {
            modal: true,
            draggable: false,
        },
        data: {
            thresholds: thresholdMap[letter],
            parameterMap,
        }
    });
}

onMounted(() => {
    mapParameters();
});
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
    <div class="flex justify-center gap-2">
        <Button v-for="letter in reports" @click="openReport(letter)" class="font-mono">
            Informe {{ letter }}
        </Button>
    </div>
    <DataTable :value="sample.analyses" class="font-mono">
        <Column header="Análisis" field="parameter.name"/>
        <Column header="Resultado calculado"/>
        <Column header="Unidad" field="parameter.unit"/>
        <Column header="Limite de cuantificación" field="parameter.quantification"/>
        <Column header="Incertidumbre" field="parameter.uncertainty"/>
        <Column header="Resultado reportado"/>
    </DataTable>
    <DynamicDialog/>
</template>
