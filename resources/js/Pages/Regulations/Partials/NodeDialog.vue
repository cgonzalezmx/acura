<script setup lang="ts">
import { useTree } from '@/Components/Tree/useTree';
import InputText from 'primevue/inputtext';
import IftaLabel from 'primevue/iftalabel';
import Button from 'primevue/button';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import { pick } from '@/utils/object';
import { computed, inject, onMounted, reactive, ref, Ref } from 'vue';
import axios from 'axios';

interface NodeData {
    name: string;
    alias: string;
    takes?: number;
    price?: number;
    lab_matrix_id?: number;
    [key: string]: any;
}

interface LabMatrix {
    name: string;
    code: string;
}

type NodeType = 'node' | 'regulation' | 'bundle';

const { selection, nodeAction, newNodeType, append, pathToSelection } = useTree('reg_tree');
const lab_matrices = ref<LabMatrix[]>();
const nodeType = computed<NodeType>(() => nodeAction.value === 'creating'
    ? newNodeType.value as NodeType
    : selection.value?.type as NodeType);
const isRegulation = () =>isNodeType('regulation');
const isBundle = () => isNodeType('bundle');
const dialogRef: Ref<any> | undefined = inject('dialogRef');
const resources = {
    node: 'regulations.nodes',
    regulation: 'regulations.definitions',
    bundle: 'regulations.bundles'
};

const defaultValues: NodeData = {
    name: '',
    alias: '',
    takes: 1,
    price: 0,
    lab_matrix_id: 1
};

const getInitialValue = (value: string | number | undefined, byDefault: keyof NodeData) =>
    nodeAction.value === 'creating' || !value
    ? defaultValues[byDefault]
    : value;

const form = reactive<NodeData>({
    name: getInitialValue(selection.value?.label, 'name'),
    alias: getInitialValue(selection.value?.alias, 'alias'),
    takes: getInitialValue(selection.value?.bundle?.takes, 'takes'),
    price: getInitialValue(selection.value?.bundle?.price, 'price'),
    lab_matrix_id: getInitialValue(selection.value?.regulation?.matrix?.id, 'lab_matrix_id')
});

function isNodeType(type: string): boolean {
    if (nodeAction.value === 'creating') {
        return newNodeType.value === type;
    }

    return selection.value?.type === type;
}

function transformData(requestData: NodeData) {
    const data = {
        ...pick(requestData as NodeData, ['name', 'alias']),
        type: nodeType.value,
        parent_id: nodeAction.value === 'creating' ? selection.value?.key : undefined
    };

    const bind = {
        regulation: () => pick(requestData, ['lab_matrix_id']),
        bundle: () => {
            const regulation_id = pathToSelection.value.find((node) => node.type === 'regulation')?.regulation?.id;
            return { ...pick(requestData, ['takes', 'price']), regulation_id }
        },
        node: () => nodeAction.value === 'creating' ? { parent_id: selection.value?.key } : null
    };

    if (nodeType.value) {
        return {
            ...data,
            ...bind[nodeType.value]()
        };
    }

    return data;
}

function createNode(resource: string) {
    axios.post(route(resource + '.store'), transformData(form))
        .then((response) => {
            append(response.data);
        });
}

function editNode(resource: string) {
    if (!selection.value) return;

    const isBundle = nodeType.value === 'bundle';
    const resourceId = isBundle ? selection.value.bundle.id : selection.value.key;

    axios.patch(route(resource + '.update', resourceId), transformData(form))
        .then(({data}) => {
            if (!selection.value) return;

            selection.value.label = data.label;
            selection.value.alias = data.alias;

            if (isBundle) {
                selection.value.bundle.price = data.bundle.price;
                selection.value.bundle.takes = data.bundle.takes;
            }
        });
}

function submit() {
    const res = resources[nodeType.value];
    nodeAction.value === 'creating' && createNode(res);
    nodeAction.value === 'editing' && editNode(nodeType.value === 'bundle' ? resources.bundle : resources.node);
    dialogRef?.value.close?.();
}

function autoselectIntpuText(event: Event) {
    (event.target as HTMLInputElement).select();
}

onMounted(async () => {
    lab_matrices.value = (await axios.get(route('matrices.index'))).data;
});
</script>

<template>
    <form @submit.prevent="submit" class="flex flex-col gap-3">
        <IftaLabel>
            <InputText v-model="form.name" required/>
            <label>Nombre</label>
        </IftaLabel>

        <IftaLabel>
            <InputText v-model="form.alias"/>
            <label>Identificador opcional</label>
        </IftaLabel>

        <IftaLabel v-if="isRegulation()">
            <Select
                v-model="form.lab_matrix_id"
                option-label="code"
                option-value="id"
                :options="lab_matrices"
                :disabled="nodeAction === 'editing'"/>
            <label>Matriz</label>
        </IftaLabel>

        <template v-if="isBundle()">
            <IftaLabel>
                <InputNumber v-model="form.takes" :min="1" @focus="autoselectIntpuText"/>
                <label>No. de tomas</label>
            </IftaLabel>
            <IftaLabel>
                <InputNumber
                    v-model="form.price"
                    mode="currency"
                    currency="MXN"
                    locale="es-MX"
                    :min="0"
                    @focus="autoselectIntpuText"/>
                <label>Precio</label>
            </IftaLabel>
        </template>

        <Button label="Guardar" type="submit" icon="fa-solid fa-floppy-disk"/>
    </form>
</template>