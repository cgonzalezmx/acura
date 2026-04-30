<script setup lang="ts">
import { useTree } from '@/Components/Tree/useTree';
import InputText from 'primevue/inputtext';
import IftaLabel from 'primevue/iftalabel';
import Button from 'primevue/button';
import { computed, inject, reactive, Ref } from 'vue';
import axios from 'axios';

type NodeType = 'node' | 'definition';

interface NodeData {
    name: string;
    alias: string;
}

const { selection, nodeAction, newNodeType, append } = useTree('instance_tree');
const { selection: structureSelection } = useTree('reg_tree');
const nodeType = computed<NodeType>(() => nodeAction.value === 'creating'
    ? newNodeType.value as NodeType
    : selection.value?.type as NodeType);
const dialogRef: Ref<any> | undefined = inject('dialogRef');
const resource = 'regulations.instances.nodes';
const inputs: { label: string, field: keyof NodeData }[] = [
    { label: 'Nombre', field: 'name' },
    { label: 'Identificador opcional', field: 'alias' }
];

const getStringValue = (value: string | undefined) =>
    nodeAction.value === 'creating' || !value ? '' : value;

const form = reactive<NodeData>({
    name: getStringValue(selection.value?.label),
    alias: getStringValue(selection.value?.alias)
});

const transformData = (requestData: NodeData) => ({
    ...requestData,
    type: nodeType.value,
    parent_id: nodeAction.value === 'creating' ? selection.value?.key : undefined,
    regulation_id: structureSelection.value?.regulation.id
})

function createNode() {
    axios.post(route(`${resource}.store`), transformData(form))
        .then(({data}) => append(data));
}

function updateNode() {
    if (!selection.value) return;

    axios.patch(route(`${resource}.update`, selection.value.key), transformData(form))
        .then(({data}) => {
            if (!selection.value) return;

            selection.value.label = data.label;
            selection.value.alias = data.alias;
        });
}

function submit() {
    nodeAction.value === 'creating' && createNode();
    nodeAction.value === 'editing' && updateNode();
    dialogRef?.value.close?.();
}
</script>

<template>
<form @submit.prevent="submit" class="flex flex-col gap-3">
    <IftaLabel v-for="{label, field} in inputs" :key="field">
        <InputText v-model="form[field]"/>
        <label>{{ label }}</label>
    </IftaLabel>

    <Button label="Guardar" type="submit" icon="fa-solid fa-floppy-disk"/>
</form>
</template>