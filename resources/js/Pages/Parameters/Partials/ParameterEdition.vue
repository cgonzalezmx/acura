<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import IftaLabel from 'primevue/iftalabel';
import Button from 'primevue/button';
import { PageProps } from '@/types';
import { computed, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { pick } from '@/utils/object';
import { Parameter } from '@/types/parameter';
import MultiSelect from 'primevue/multiselect';
import Checkbox from 'primevue/checkbox';

interface LocalPageProps extends PageProps {
    analysisAreas: any[],
    parameterCategories: any[],
    labMatrices: any[],
    methodologies: any[],
    samplePreservers: any[],
    measurementUnits: any[],
    sampleStorages: any[],
    sampleContainers: any[],
    labelColors: any[],
    quoteRemarks: any[],
    samplingRemarks: any[]
}

const props =  defineProps(['value']);
const page = usePage<LocalPageProps>();
const toast = useToast();
const errors = computed(() => page.props.errors);
const emit = defineEmits(['success']);
const values: Parameter = {
    id: 0,
    name: '',
    price: 0,
    unit_volume: '',
    group_volume: '',
    parameter_category_id: 1,
    lab_matrix_id: 1,
    analysis_area_id: 0,
    methodology_id: 0,
    measurement_unit_id: 0,
    sample_container_id: 0,
    sample_preserver_id: 1,
    sample_storage_id: 0,
    label_color_id: 0,
    quote_remarks: [],
    sampling_remarks: [],
    multiple: false
};
const form = useForm(props.value ? pick(props.value, Object.keys(values) as Array<keyof Parameter>) : values);

function submit() {
    const showToast = (detail: string) => toast.add({
        severity: 'success',
        life: 3000,
        detail
    });

    if (form.id > 0) {
        form.put(route('parameters.update', form.id), {
            only: ['parameters'],
            onSuccess() {
                showToast(`Parámetro actualizado: ${form.name}.`);
                emit('success');
            }
        });

        return
    }

    form.post(route('parameters.store'), {
        only: ['parameters'],
        onSuccess() {
            showToast(`Parámetro creado: ${form.name}.`);
            emit('success');
        }
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="mx-7">
        <div class="grid grid-cols-2 gap-6 mt-2 mb-3">
            <IftaLabel class="col-span-2">
                <InputText
                    v-model="form.name"
                    :invalid="typeof errors.name !== 'undefined'"
                    class="w-full"/>
                <label>Nombre</label>
            </IftaLabel>
            <IftaLabel>
                <Select
                    v-model="form.lab_matrix_id"
                    :options="page.props.labMatrices"
                    option-label="code"
                    option-value="id"
                    class="w-full"/>
                <label>Matrix</label>
            </IftaLabel>
            <div class="flex items-center">
                <Checkbox v-model="form.multiple" binary/>
                <label>Varias tomas</label>
            </div>
            <IftaLabel>
                <Select
                    v-model="form.analysis_area_id"
                    :options="page.props.analysisAreas"
                    option-label="name"
                    option-value="id"
                    class="w-full"/>
                <label>Área de análisis</label>
            </IftaLabel>
            <IftaLabel>
                <InputNumber
                    pt:pc-input-text:root="w-full"
                    pt:root:class="w-full"
                    v-model="form.price"
                    locale="es-MX"
                    mode="currency"
                    currency="MXN"/>
                <label>Precio</label>
            </IftaLabel>
            <IftaLabel class="col-span-2">
                <Select
                    v-model="form.methodology_id"
                    :options="page.props.methodologies"
                    filter
                    option-label="name"
                    option-value="id"
                    class="w-full"/>
                <label>Método</label>
            </IftaLabel>
            <IftaLabel>
                <Select
                    v-model="form.measurement_unit_id"
                    :options="page.props.measurementUnits"
                    option-label="unit"
                    option-value="id"
                    class="w-full"/>
                <label>Unidades</label>
            </IftaLabel>
            <IftaLabel>
                <Select
                    v-model="form.sample_storage_id"
                    :options="page.props.sampleStorages"
                    option-label="identifier"
                    option-value="id"
                    class="w-full"/>
                <label>Almacenador</label>
            </IftaLabel>
            <IftaLabel>
                <Select
                    v-model="form.sample_container_id"
                    :options="page.props.sampleContainers"
                    option-label="name"
                    option-value="id"
                    class="w-full"/>
                <label>Recipiente</label>
            </IftaLabel>
            <IftaLabel>
                <Select
                    v-model="form.label_color_id"
                    :options="page.props.labelColors"
                    option-label="color"
                    option-value="id"
                    class="w-full"/>
                <label>Etiqueta</label>
            </IftaLabel>
            <IftaLabel class="col-span-2">
                <Select
                    v-model="form.sample_preserver_id"
                    :options="page.props.samplePreservers"
                    option-label="name"
                    option-value="id"
                    class="w-full"/>
                <label>Preservador</label>
            </IftaLabel>
            <IftaLabel>
                <InputText v-model="form.unit_volume" class="w-full"/>
                <label>Volumen unitario</label>
            </IftaLabel>
            <IftaLabel>
                <InputText v-model="form.group_volume" class="w-full"/>
                <label>Volumen grupal</label>
            </IftaLabel>
            <IftaLabel>
                <MultiSelect
                    v-model="form.quote_remarks"
                    option-label="code"
                    option-value="id"
                    :options="page.props.quoteRemarks"
                    class="w-full"
                    >
                    <template #option="slotProps">
                        <div v-tooltip="slotProps.option.description" class="w-full">{{ slotProps.option.code }}</div>
                    </template>
                </MultiSelect>
                <label>Observaciones cotización</label>
            </IftaLabel>
            <IftaLabel>
                <MultiSelect
                    v-model="form.sampling_remarks"
                    option-label="code"
                    option-value="id"
                    :options="page.props.samplingRemarks"
                    class="w-full">
                    <template #option="slotProps">
                        <div v-tooltip="slotProps.option.description" class="w-full">{{ slotProps.option.code }}</div>
                    </template>
                </MultiSelect>
                <label>Observaciones de muestreo</label>
            </IftaLabel>
        </div>
        <Button type="submit" label="Guardar" icon="fa-solid fa-floppy-disk"/>
    </form>
</template>