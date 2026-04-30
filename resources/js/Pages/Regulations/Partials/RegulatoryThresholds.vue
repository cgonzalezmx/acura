<script setup lang="ts">
import { useTree } from '@/Components/Tree/useTree';
import DataTable, { DataTableRowEditSaveEvent } from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import { computed, reactive, ref, watch } from 'vue';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { FilterMatchMode } from '@primevue/core/api';
import axios from 'axios';

interface Parameter {
    id: number;
    name: string;
    area: string;
}

interface Threshold {
    id: number;
    min: string;
    max: string;
}

interface Entry {
    parameter_id: number;
    threshold: Threshold;
    [key: string]: any;
}

const editingRows = ref([]);
const { selection: definition } = useTree('reg_tree');
const { selection: instance } = useTree('instance_tree');
const filters = ref({ name: { value: null, matchMode: FilterMatchMode.STARTS_WITH } });
const thresholds = reactive(new Map<number, Threshold>());

const entries = computed(() => {
    if (definition.value) {
        return definition.value.regulation.parameters.map((param: Parameter) => ({
            parameter_id: param.id,
            name: param.name,
            area: param.area,
            threshold: thresholds.get(param.id) ?? {id: null, min: '', max: ''}
        }));
    }

    return [];
});

function create(entry: Entry ) {
    const requestData = {
        regulation_id: definition.value?.regulation.id,
        regulation_instance_id: instance.value?.key,
        parameter_id: entry.parameter_id,
        min: entry.threshold.min,
        max: entry.threshold.max
    };

    axios.post(route('regulations.thresholds.store'), requestData)
        .then((response) => {
            const [param_id, value] = response.data;
            instance.value?.thresholds.push([param_id, value]);
            thresholds.set(param_id, value);
            editingRows.value = [];
        });
}

function update(entry: Entry) {
    const { threshold } = entry;
    const requestData = {
        min: threshold.min,
        max: threshold.max
    };

    axios.patch(route('regulations.thresholds.update', threshold.id), requestData)
        .then(() => editingRows.value = []);
}

function onRowEditSave(event: DataTableRowEditSaveEvent) {
    if (!instance.value) return;

    const { newData } = event;

    if (!newData.threshold.id) {
        create(newData);
    }
    else {
        update(newData);
    }
}

watch(instance, (newInstance) => {
    thresholds.clear();

    if (newInstance?.type === 'definition') {
        newInstance?.thresholds.forEach(([key, value]: [key: number, value: Threshold]) => {
            thresholds.set(key, value);
        });
    }
}, { immediate: true });
</script>

<template>
    <DataTable
        v-model:editing-rows="editingRows"
        v-model:filters="filters"
        edit-mode="row"
        paginator
        data-key="parameter_id"
        scrollable
        scroll-height="flex"
        pt:root:class="flex flex-col overflow-hidden w-full"
        :value="entries"
        :rows="10"
        @row-edit-save="onRowEditSave">
        <template #header>
            <IconField>
                <InputIcon class="fa-solid fa-magnifying-glass"/>
                <InputText v-model="filters.name.value" placeholder="Buscar..."/>
            </IconField>
        </template>
        <ColumnGroup type="header">
            <Row>
                <Column header="Parámetro" :rowspan="2"/>
                <Column header="Área" :rowspan="2"/>
                <Column header="Límites permisibles" :colspan="2"/>
                <Column :rowspan="2"/>
            </Row>
            <Row>
                <Column header="Min"/>
                <Column header="Max"/>
            </Row>
        </ColumnGroup>
        <Column field="name"/>
        <Column field="area"/>
        <Column field="threshold.min">
            <template #editor="{ data }">
                <InputText v-model="data.threshold.min" class="w-full" size="smal"/>
            </template>
        </Column>
        <Column field="threshold.max">
            <template #editor="{ data }">
                <InputText v-model="data.threshold.max" class="w-full" size="smal"/>
            </template>
        </Column>
        <Column :row-editor="instance?.type === 'definition'">
            <template #roweditoriniticon>
                <i class="fa-solid fa-pen"></i>
            </template>
            <template #roweditorsaveicon>
                <i class="fa-solid fa-floppy-disk"></i>
            </template>
            <template #roweditorcancelicon>
                <i class="fa-solid fa-xmark"></i>
            </template>
        </Column>
    </DataTable>
</template>