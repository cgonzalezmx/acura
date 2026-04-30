<script setup lang="ts">
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import MultiSelect from 'primevue/multiselect';
import { usePage } from '@inertiajs/vue3';
import { PageProps } from '../types';
import { useProcedureStore } from '../store';

const pageProps = usePage<PageProps>().props;
const store = useProcedureStore();
</script>

<template>
    <div class="grid grid-cols-3 gap-3 border border-slate-200 rounded-md p-4 items-center bg-slate-100">
        <IftaLabel>
            <InputText v-model="store.procedure.name" size="small"/>
            <label>Lote</label>
        </IftaLabel>
        <IftaLabel>
            <DatePicker v-model="store.procedure.checkout_time" timeOnly size="small"/>
            <label>Salida del refrigerador</label>
        </IftaLabel>
        <IftaLabel>
            <InputText v-model="store.procedure.log" size="small"/>
            <label>Bitácora</label>
        </IftaLabel>
        <IftaLabel>
            <DatePicker v-model="store.procedure.analyzed_at" size="small" show-time hour-format="24" :step-minute="5"/>
            <label>Fecha y hora de análisis</label>
        </IftaLabel>
        <IftaLabel>
            <DatePicker v-model="store.procedure.checkin_time" size="small" timeOnly/>
            <label>Regreso a refrigeradores</label>
        </IftaLabel>
        <IftaLabel>
            <InputText v-model="store.procedure.solutions_log" size="small"/>
            <label>Bitácora de soluciones</label>
        </IftaLabel>
        <div class="flex">
            <div class="font-medium pr-2">No. de muestras:</div>
            <div>{{ pageProps.batch.analysis_count }}</div>
        </div>
        <IftaLabel>
            <MultiSelect
                v-model="store.procedure.sample_storages"
                :options="pageProps.refrigerators"
                option-label="identifier"
                option-value="id"
            />
            <label>Refrigeradores</label>
        </IftaLabel>
        <div class="flex">
            <div class="font-medium pr-2">LMC:</div>
            <div>{{ pageProps.min_quantifiable }} {{ pageProps.measurement_unit}}</div>
        </div>
    </div>
</template>

<style scoped>

</style>
