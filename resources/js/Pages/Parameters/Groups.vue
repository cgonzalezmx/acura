<script setup lang="ts">
import DataTable, { DataTableRowSelectEvent } from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import { useToggle } from '@vueuse/core';
import { computed, defineAsyncComponent, onMounted, provide, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import PopupMenu from '@/Components/PopupMenu.vue';
import Panel from 'primevue/panel';
import MultiSelect, { MultiSelectChangeEvent } from 'primevue/multiselect';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const [showDialog, toggleDialog] = useToggle();

interface Props {
    items?: any[];
    sampleContainers: any[];
    samplePreservers: any[];
    labelColors: any[];
    parameters: any[];
    analysisAreas: any[];
    labMatrices: any[];
}

const props = defineProps<Props>();
const GroupEdition = defineAsyncComponent(() => import('./Partials/GroupEdition.vue'));
const containerMap = new Map(props.sampleContainers.map((sc) => [sc.id, sc.name]));
const preserverMap = new Map(props.samplePreservers.map((sp) => [sp.id, sp.name]))
const labelColorMap = new Map(props.labelColors.map((lc) => [lc.id, lc.color]));
const analysisAreaMap = new Map(props.analysisAreas.map((aa) => [aa.id, aa.name]));
const matrixMap = new Map(props.labMatrices.map((m) => [m.id, m.code]));
const parameterMap = new Map(props.parameters?.map((p) => [p.id, p]) ?? [])
const currentGroup = ref();
const parametersInGroup = ref([]);
const toast = useToast();
const listedParameters = computed(() => parametersInGroup.value.map((param) => parameterMap.get(param)));

function edit(group: any) {
    currentGroup.value = group;
    toggleDialog();
}

function create() {
    currentGroup.value = null;
    toggleDialog();
}

function queryGroup(event: DataTableRowSelectEvent) {
    const { data } = event;
    axios.get(route('parameter-groups.show', data.id))
        .then(({data}) => {
            parametersInGroup.value = data.map((item: any) => item.id);
        });
}

function onUnselect() {
    parametersInGroup.value = [];
}

function syncToGroup() {
    if (!currentGroup.value) {
        toast.add({
            detail: 'Seleccione un grupo',
            severity: 'warn',
            life: 3000
        });
        return;
    }

    axios.post(route('parameter-groups.sync', currentGroup.value.id), {
        id_payload: parametersInGroup.value
    });
}

function onMultiSelectChange(event: MultiSelectChangeEvent) {
    parametersInGroup.value = event.value;
}

function deleteGroup(id: number) {
    router.delete(route('parameter-groups.destroy', id), {
        only: ['groups']
    });
}

provide('containers', props.sampleContainers);
provide('preservers', props.samplePreservers);
provide('labelColors', props.labelColors);

onMounted(() => {
    if (!props.items) {
        router.reload({
            only: ['groups']
        })
    }
});
</script>

<template>
    <Head title="Grupos"/>
    <Toolbar class="mb-4">
        <template #start>
            <Button  label="Nuevo" @click="create"/>
        </template>
    </Toolbar>
    <div class="flex flex-col xl:flex-row gap-4">
        <Panel class="xl:w-1/2 overflow-auto" header="Lista de grupos">
            <DataTable
                :value="items"
                v-model:selection="currentGroup"
                data-key="id"
                selection-mode="single"
                scrollable
                @row-select="queryGroup"
                @row-unselect="onUnselect">
                <Column>
                    <template #body="{data}">
                        <PopupMenu :model="[
                            {
                                label: 'Editar',
                                icon: 'fa-solid fa-file-pen',
                                command: () => edit(data)
                            },
                            {
                                label: 'Borrar',
                                icon: 'fa-solid fa-trash-can',
                                command: () => deleteGroup(data.id)
                            }
                        ]"
                        severity="secondary"/>
                    </template>
                </Column>
                <Column header="Grupo" field="name"/>
                <Column header="Volumen" field="required_sample_volume"/>
                <Column header="Contenedor">
                    <template #body="{data}">
                        {{ containerMap.get(data.sample_container_id) }}
                    </template>
                </Column>
                <Column header="Preservador">
                    <template #body="{data}">
                        {{ preserverMap.get(data.sample_preserver_id) }}
                    </template>
                </Column>
                <Column header="Etiqueta">
                    <template #body="{data}">
                        {{ labelColorMap.get(data.label_color_id) }}
                    </template>
                </Column>
            </DataTable>
        </Panel>
        <Panel class="xl:w-1/2">
            <DataTable :value="listedParameters" scrollable>
                <template #header>
                    <div class="flex justify-between">
                        <MultiSelect
                            :model-value="parametersInGroup"
                            @change="onMultiSelectChange"
                            @update:model-value=""
                            :options="parameters"
                            option-value="id"
                            option-label="name"
                            :max-selected-labels="0"
                            filter
                            class="w-full">
                            <template #option="slotProps">
                                <div class="grid grid-cols-[200px_200px_200px]">
                                    <div class="text-wrap">{{ slotProps.option.name }}</div>
                                    <div>{{ analysisAreaMap.get(slotProps.option.analysis_area_id) }}</div>
                                    <div>{{ matrixMap.get(slotProps.option.lab_matrix_id) }}</div>
                                </div>
                            </template>
                        </MultiSelect>
                        <Button label="Añadir" @click="syncToGroup"/>
                    </div>
                </template>
                <Column header="Parámetro">
                    <template #body="{data}">
                        {{ parameterMap.get(data.id).name }}
                    </template>
                </Column>
                <Column>
                    <template #body="{data}">
                        {{ matrixMap.get(parameterMap.get(data.id).lab_matrix_id) }}
                    </template>
                </Column>
                <Column>
                    <template #body="{data}">
                        {{ analysisAreaMap.get(parameterMap.get(data.id).analysis_area_id) }}
                    </template>
                </Column>
            </DataTable>
        </Panel>
    </div>
    <Dialog
        v-model:visible="showDialog"
        :draggable="false"
        modal>
        <GroupEdition :current="currentGroup"/>
    </Dialog>
</template>