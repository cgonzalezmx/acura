<script setup lang="ts">
import { h, ref, computed, defineAsyncComponent } from 'vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import { FilterMatchMode } from '@primevue/core/api';
import { currency } from '@/utils/formatters'
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { Parameter } from '@/types/parameter';
import ConfirmDialog from 'primevue/confirmdialog';

interface ColumnAttributes {
    field: string;
    filteredField: string;
    header: string;
    options: any;
    optionLabel: string;
    placeholder: string;
}

defineOptions({ layout: [HomeLayout]});
const ParameterEdition = defineAsyncComponent(() => import('./Partials/ParameterEdition.vue'))
const props = defineProps([
    'parameters',
    'labMatrices',
    'methodologies',
    'sampleContainers',
    'analysisAreas',
    'labelColors',
    'samplePreservers',
    'sampleStorages',
    'quoteRemarks',
    'samplingRemarks'
]);

const columns: ColumnAttributes[] = [
    {
        field: 'analysis_area',
        filteredField: 'analysis_area_id',
        header: 'Área de análisis',
        optionLabel: 'name',
        options: props.analysisAreas,
        placeholder: 'Buscar área'
    },
    {
        field: 'methodology',
        filteredField: 'methodology_id',
        header: 'Método',
        optionLabel: 'name',
        options: props.methodologies,
        placeholder: 'Buscar método'
    },
    {
        field: 'sample_container',
        filteredField: 'sample_container_id',
        header: 'Recipiente',
        optionLabel: 'name',
        options: props.sampleContainers,
        placeholder: 'Buscar recipiente'
    }
];

const filters = ref({
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    lab_matrix_id: { value: 1, matchMode: FilterMatchMode.EQUALS },
    methodology_id: { value: null, matchMode: FilterMatchMode.EQUALS },
    sample_container_id: { value: null, matchMode: FilterMatchMode.EQUALS },
    analysis_area_id: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const parametersList = computed(() => [...new Set(props.parameters.map((item: any) => item.name))]);
const expandedRows = ref({});
const showParameterEdition = ref(false);
const parameter = ref<Parameter>();
const toast = useToast();
const confirm = useConfirm();
const deleteParameter = (param: any) => {
    confirm.require({
        header: '¡Advertencia!',
        message: `¿Desea eliminar ${param.name}?`,
        icon: 'fa-solid fa-circle-exclamation',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary'
        },
        acceptProps: {
            label: 'Confirmar'
        },
        accept() {
            router.delete(route('parameters.destroy', param.id), { only: ['parameters'] });
            filters.value.name.value = null;
            toast.add({
                severity: 'info',
                detail: `Se ha borrado ${param.name}`,
                life: 3000
            });
        }
    })
}

const ParameterData = (props: { header: string, data: any}) => h('div', [
    h('div', { class: 'font-bold' }, props.header),
    h('div', props.data)
]);

function editParameter(param: Parameter) {
    parameter.value = param;
    showParameterEdition.value = true;
}
</script>

<template>
    <Head title="Parámetros"/>
    <ConfirmDialog pt:root:class="w-80" :draggable="false" />
    <Toolbar class="mb-6">
        <template #start>
            <Button
                icon="fa-regular fa-plus"
                label="Nuevo"
                @click="showParameterEdition = true"
                class="mr-3"/>
        </template>
        <template #end>
            <Button
                label="Exportar"
                severity="secondary"/>
        </template>
    </Toolbar>
    <DataTable
        v-model:expanded-rows="expandedRows"
        data-key="id"
        :value="parameters"
        paginator
        scrollable
        v-model:filters="filters"
        filter-display="row"
        :rows="25"
        :rows-per-page-options="[25, 50, 100]"
        pt:root:class="flex flex-col overflow-hidden w-full"
        :pt="{
            rowExpansion: {
                class: 'bg-slate-50'
            }
        }">
        <template #header>
            <div class="flex justify-between">
                <h4>Lista de Parámetros del sistema</h4>
            </div>
        </template>
        <Column expander style="width: 5rem"/>
        <Column
            field="name"
            header="Parámetro"
            filter-field="name"
            :show-filter-menu="false">
            <template #filter="{ filterModel, filterCallback }">
                <Select
                    v-model="filterModel.value"
                    :options="parametersList"
                    placeholder="Buscar parámetro"
                    filter
                    @change="filterCallback"/>
            </template>
        </Column>
        <Column
            field="lab_matrix"
            header="Matriz"
            filter-field="lab_matrix_id"
            :show-filter-menu="false">
            <template #filter="{ filterModel, filterCallback }">
                <Select
                    v-model="filterModel.value"
                    :options="labMatrices"
                    option-label="code"
                    option-value="id"
                    @change="filterCallback"/>
            </template>
        </Column>
        <Column
            v-for="col in columns"
            :key="col.field"
            :field="col.field"
            :filter-field="col.filteredField"
            :header="col.header"
            :show-filter-menu="false">
            <template #filter="{ filterModel, filterCallback }">
                <Select
                    v-model="filterModel.value"
                    :options="col.options"
                    :option-label="col.optionLabel"
                    option-value="id"
                    filter
                    :placeholder="col.placeholder"
                    show-clear
                    @change="filterCallback"/>
            </template>
        </Column>
        <Column>
            <template #body="{ data }">
                <Button
                    v-tooltip.top="{ value: 'Editar', showDelay: 1000, hideDelay: 300 }"
                    icon="fa-solid fa-pen"
                    class="mr-2"
                    @click="editParameter(data)"/>
            </template>
        </Column>
        <Column>
            <template #body="{ data }">
                <Button
                    v-tooltip.top="{ value: 'Borrar', showDelay: 1000, hideDelay: 300 }"
                    icon="fa-regular fa-trash-can"
                    severity="danger"
                    outlined
                    @click="deleteParameter(data)"/>
            </template>
        </Column>
        <template #expansion="slotProps">
            <div class="grid auto-cols-max grid-flow-col gap-7">
                <ParameterData header="Precio" :data="currency(slotProps.data.price)"/>
                <ParameterData header="Unidad" :data="slotProps.data.measurement_unit"/>
                <ParameterData header="Almacenador" :data="slotProps.data.sample_storage"/>
                <ParameterData header="Volumen unitario" :data="slotProps.data.unit_volume"/>
                <ParameterData header="Volumen grupal" :data="slotProps.data.group_volume"/>
                <ParameterData header="Color de etiqueta" :data="slotProps.data.label_color"/>
                <ParameterData header="Preservador" :data="slotProps.data.sample_preserver"/>
                <div class="col-span-2 grid grid-cols-subgrid gap-x-2">
                    <div class="font-bold col-span-2">Cuantificación</div>
                    <div>Rango bajo:</div>
                    <div>{{ slotProps.data.quantification_low_range }}</div>
                    <div>Rango medio:</div>
                    <div>{{ slotProps.data.quantification_mid_range }}</div>
                    <div>Rango alto:</div>
                    <div>{{ slotProps.data.quantification_high_range }}</div>
                </div>
                <div class="col-span-2 grid grid-cols-subgrid gap-x-2">
                    <div class="font-bold col-span-2">Incertidumbre</div>
                    <div>Rango bajo:</div>
                    <div>{{ slotProps.data.uncertainty_low_range }}</div>
                    <div>Rango medio:</div>
                    <div>{{ slotProps.data.uncertainty_mid_range }}</div>
                    <div>Rango alto:</div>
                    <div>{{ slotProps.data.uncertainty_high_range }}</div>
                </div>
            </div>
        </template>
    </DataTable>
    <Dialog
        v-model:visible="showParameterEdition"
        header="Datos del parámetro"
        modal
        :draggable="false"
        class="xl:w-1/3 lg:w-2/5 md:w-2/3"
        @hide="parameter = undefined">
        <ParameterEdition
            :value="parameter"
            @success="showParameterEdition = false"/>
    </Dialog>
</template>