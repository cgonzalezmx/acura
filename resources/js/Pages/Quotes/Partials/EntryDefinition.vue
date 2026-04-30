<script setup lang="ts">
import { useEntryStore } from '../stores/useEntryStore';
import Textarea from 'primevue/textarea';
import RadioButton from 'primevue/radiobutton';
import DatePicker from 'primevue/datepicker';
import InputNumber from 'primevue/inputnumber';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';
import Reports from './Reports.vue';
import { onMounted, ref, watch } from 'vue';
import { useQuoteDataStore } from '../stores/useQuoteDataStore';
import { storeToRefs } from 'pinia';
import Checkbox from 'primevue/checkbox';

interface Props {
    entryId: string;
}

const { entryId } = defineProps<Props>();
const { sampleDeliveredByClient } = storeToRefs(useQuoteDataStore());
const entryStore = useEntryStore();
const entry = entryStore.get(entryId);
const formFactors = [
    'Registro',
    'Tubería',
    'Grifo',
    'Canal abierto',
    'No especificado'
];
const otherFormFactor = ref('');
const selectedFormFactor = ref('');

watch([() => selectedFormFactor.value, () => otherFormFactor.value], ([formFactor, other]) => {
    if (formFactor === 'other') {
        entry.form_factor = other;
        return;
    }

    entry.form_factor = formFactor;
});

onMounted(() => {
    if (entry.form_factor) {
        if (formFactors.find((form) => form === entry.form_factor)) {
            selectedFormFactor.value = entry.form_factor;
        }
        else {
            selectedFormFactor.value = 'other';
            otherFormFactor.value = entry.form_factor;
        }
    }
});
</script>

<template>
    <div class="grid grid-cols-4 gap-3">
        <IftaLabel class="col-span-3">
            <Textarea v-model="entry.title" class="resize-none w-full"/>
            <label>Nombre del punto de muestreo</label>
        </IftaLabel>
        <div class="flex items-center">
            <Checkbox v-model="entry.is_urgent" binary/>
            <span class="ml-3">Urgente</span>
        </div>
        <div class="col-span-4 flex justify-between">
            <span>Forma del punto de muestreo:</span>
            <div
                v-for="form in formFactors"
                :key="form"
                class="flex items-center"
                >
                <RadioButton v-model="selectedFormFactor" :value="form"/>
                <span class="ml-3">{{ form }}</span>
            </div>
            <div class="flex items-center">
                <RadioButton v-model="selectedFormFactor" :value="'other'"/>
                <span class="mx-3">Otro</span>
                <InputText v-model="otherFormFactor" size="small"/>
            </div>
        </div>
        <template v-if="sampleDeliveredByClient">
                <IftaLabel>
                    <InputText v-model="entry.sample_type" fluid />
                    <label>Tipo de muestra</label>
                </IftaLabel>
                <IftaLabel>
                    <DatePicker v-model="entry.sample_reception_date" show-time hour-format="12" fluid update-model-type="date" show-button-bar/>
                    <label>Fecha y hora de recepción</label>
                </IftaLabel>
                <IftaLabel>
                    <DatePicker v-model="entry.sampling_date" show-time hour-format="12" fluid update-model-type="date" show-button-bar/>
                    <label>Fecha y hora de muestreo</label>
                </IftaLabel>
                <InputGroup>
                    <IftaLabel>
                        <InputNumber v-model="entry.sample_temperature" fluid />
                        <label>Temperatura de ingreso</label>
                    </IftaLabel>
                    <InputGroupAddon>°C</InputGroupAddon>
                </InputGroup>
                <IftaLabel>
                    <InputText v-model="entry.sample_container_type" fluid />
                    <label>Tipo de contenedor</label>
                </IftaLabel>
                <IftaLabel>
                    <InputNumber v-model="entry.total_containers" fluid :min="1"/>
                    <label>Total de recipientes</label>
                </IftaLabel>
                <IftaLabel>
                    <InputText v-model="entry.total_volume" fluid />
                    <label>Vólumen total</label>
                </IftaLabel>
                <div class="flex items-center">
                    <Checkbox v-model="entry.refrigerated" binary />
                    <span class="ml-3">Identificadas y refrigeradas</span>
                </div>
                <IftaLabel class="col-span-2">
                    <Textarea v-model="entry.observation" fluid class="resize-none"/>
                    <label>Observaciones</label>
                </IftaLabel>
        </template>
        <IftaLabel class="col-span-4">
            <Textarea v-model="entry.objective" fluid class="resize-none" />
            <label>Objetivo de la prueba</label>
        </IftaLabel>
        <div>
            <div class="font-semibold">Tiempo de entrega de informes</div>
            <InputNumber v-model="entry.result_time_lapse" :min="0" />
        </div>
    </div>
    <Reports :entry-id="entryId"/>
</template>