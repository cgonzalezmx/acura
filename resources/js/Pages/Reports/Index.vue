<script setup lang="ts">
import RangeDatePicker from '@/Components/RangeDatePicker.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Toolbar from 'primevue/toolbar';
import FetchAutoComplete from '@/Components/FetchAutoComplete.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { format } from 'date-fns';
import { Sample } from '@/types/sample';
import IftaLabel from 'primevue/iftalabel';
import { useAsyncState } from '@vueuse/core';
import axios from 'axios';

defineOptions({ layout: HomeLayout });

interface Props {
    samples?: any[];
}

const props = defineProps<Props>();

interface QueriedResult {
    id: number,
    identifier: string;
}

const queryForm = useForm({
    start: null as Date | null,
    end: null as Date | null,
    sample: '' as string | QueriedResult,
    samplingFormat: '' as string | QueriedResult,
    client: '',
});

const { state: sample, isLoading, isReady } = useAsyncState(
    axios.get(route('')).then(({ data }) => data),
    {},
    { immediate: false }
);

function filter() {
    if (Object.values(queryForm.data()).every((item) => !item)) {
        return;
    }

    queryForm
        .transform((query) => {
            const params: Record<string, any> = {};

            for (const [key, value] of Object.entries(query)) {
                switch(key) {
                    case 'sample':
                        params.sampleId = (query.sample as QueriedResult)?.id;
                        break;
                    case 'samplingFormat':
                        params.samplingFormatId = (query.samplingFormat as QueriedResult)?.id;
                        break;
                    case 'start':
                    case 'end':
                        query[key] && (params[key] = format(query[key] as Date, 'yyyy-MM-dd'));
                        break;
                    default:
                        if (value) params[key] = value;
                }
            }
            return params
        })
        .get(route('reports.index'), {only: ['samples'], preserveState: true});
}
</script>

<template>
    <div class="bg-white p-4">
        <Toolbar pt:start="flex gap-2">
            <template #start>
                <RangeDatePicker v-model:start="queryForm.start" v-model:end="queryForm.end"/>
                <IftaLabel>
                    <FetchAutoComplete
                        v-model="queryForm.sample"
                        option-label="identifier"
                        show-clear
                        :url="route('samples.search')"/>
                    <label>Muestra</label>
                </IftaLabel>
                <IftaLabel>
                    <FetchAutoComplete
                        v-model="queryForm.client"
                        show-clear
                        :url="route('quotes.clients.search')"/>
                    <label>Cliente</label>
                </IftaLabel>
                <IftaLabel>
                    <FetchAutoComplete
                        v-model="queryForm.samplingFormat"
                        option-label="identifier"
                        show-clear
                        :url="route('sampling-formats.search')"/>
                    <label>Formato de muestreo</label>
                </IftaLabel>
                <Button label="Filtrar" @click="filter"/>
            </template>
        </Toolbar>
        <table class="mt-4">
            <thead>
                <tr>
                    <th>Muestra</th>
                    <th>Total análisis</th>
                    <th>No. tomas</th>
                    <th>Fecha de muestreo</th>
                    <th>Muestreado por</th>
                    <th>Fecha de recepción</th>
                    <th>Notas de la cotización</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="sample in samples" :key="sample.id">
                    <td>{{ sample.identifier }}</td>
                    <td>{{ sample.analyses_count }}</td>
                    <td>{{ sample.takes_count }}</td>
                    <td>{{ sample.sampled_at }}</td>
                    <td>{{ sample.sampled_by }}</td>
                    <td>{{ sample.reception_date }}</td>
                    <td>{{ sample?.notes }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
