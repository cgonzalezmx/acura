<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import { Head } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import { computed, defineAsyncComponent, ref, shallowRef } from 'vue';
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';
import { usePreview } from './composables/usePreview';
import Take from './Classes/Take';
import { SampleOverview as Overview, Sampler } from './types'
import { Sample } from '@/types/sample';
import * as formatter from '@/utils/formatters'

defineOptions({ layout: HomeLayout });

interface Props {
    samples: Sample[];
    samplers: Sampler[];
}

const props = defineProps<Props>();
const show = ref(false);
const SampleOverview = defineAsyncComponent(() => import('./Partials/SampleOverview.vue'));
const preview = usePreview();
const isPreview = ref(false);
const samplers: Record<number, string> = {};
const overview = computed<Overview | null>(() => {
    return preview.preview.value;
});
const currentSample = shallowRef<Sample | null>(null);
const takes = computed(() => {
    if (isPreview.value) {
        let t = [];
        for (let i = 0; i < (preview.preview.value?.takes_count ?? 0); i++) {
            t.push(Take.empty());
        }

        return t;
    }

    if (currentSample.value?.takes) {
        return currentSample.value.takes;
    }

    return [];
});
const sampleTimestamp = (takes: Take[]) => {
    const timestamp = new Date(takes[0].timestamp!);
    const date = formatter.date(timestamp);
    const time = formatter.time(timestamp);

    return {
        date,
        time
    };
}

const samples = computed(() => {
    return props.samples.map((s) => {
        const timestamp = sampleTimestamp(s.takes);
        return {
            ...s,
            sampled_at: {
                date: timestamp.date,
                time: timestamp.time
            }
        };
    });
});

for(const s of props.samplers) {
    samplers[s.id] = s.name;
}

function displayPreview() {
    show.value = true;
    isPreview.value = true;
}

function edit(data: Sample) {
    currentSample.value = data;
    preview.query.value = data.sampling_format_id.toString();
    preview.load(data);
    show.value = true;
}

function closeDialog() {
    preview.clear();
    isPreview.value = false;
}

</script>

<template>
    <Head title="Aprobación de muestras"/>

    <h2 class="text-3xl font-semibold">Aprobación de muestras</h2>

    <DataTable :value="samples">
        <template #header>
            <Toolbar>
                <template #start>
                    <div class="flex">
                        <Button label="Ingresar muestra" @click="displayPreview"/>
                    </div>
                </template>
            </Toolbar>
        </template>
        <Column>
            <template #body="{ data }">
                <Button
                    v-tooltip="'Editar muestra'"
                    icon="fa-solid fa-file-pen"
                    severity="secondary"
                    @click="edit(data)"/>
            </template>
        </Column>
        <Column header="Muestra" field="identifier"/>
        <Column header="Fecha de recepción">
            <template #body="{ data }">
                {{ formatter.timestamp(data.reception_date) }}
            </template>
        </Column>
        <Column header="Muestreador">
            <template #body="{ data }">
                {{ samplers[data.sampled_by] }}
            </template>
        </Column>
        <Column header="No. de tomas" field="takes_count"/>
        <Column header="Fecha de muestreo" field="sampled_at.date"/>
        <Column header="Hora de muestreo" field="sampled_at.time"/>
        <Column header="Punto de muestreo" field="sampling_point"/>
        <Column header="Cliente" field="client.name"/>
    </DataTable>
    <Dialog
        v-model:visible="show"
        :draggable="false"
        modal
        @hide="closeDialog"
        :header="isPreview ? 'Ingresar muestra' : 'Editar muestra'"
        class="max-w-[90vw] xl:max-w-[60vw] max-h-[95vh]">
        <div v-if="isPreview" class="flex gap-4">
            <IftaLabel>
                <InputText v-model="preview.query.value" size="small"/>
                <label>Formato de muestreo</label>
            </IftaLabel>
            <Button label="Cargar datos" @click="preview.load()"/>
        </div>
        <SampleOverview
            v-if="overview"
            :overview
            :takes
            :is-preview
            @on-success="show = false"
        />
    </Dialog>
</template>
