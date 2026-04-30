<script setup lang="ts">
import Select from 'primevue/select';
import Button from 'primevue/button';
import Card from 'primevue/card';
import { useTree } from '@/Components/Tree/useTree';
import { computed, defineAsyncComponent, nextTick, onMounted, ref, unref } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Divider from 'primevue/divider';
import TreeNavigator from '@/Components/Tree/TreeNavigator.vue';
import TreeNodeToolbar from '@/Components/Tree/TreeNodeToolbar.vue';
import { TreeNode } from 'primevue/treenode';
import RegulatoryThresholds from './RegulatoryThresholds.vue';
import { useDialog } from 'primevue/usedialog';
import Textarea from 'primevue/textarea';

const { selection } = useTree('reg_tree');
const regulation = ref(selection.value?.regulation);
const matrixParameters = ref([]);
const parameterToAdd = ref();
const toast = useToast();
const tree = useTree('instance_tree');
const dialog = useDialog();
const dialogComponent = defineAsyncComponent(() => import('@/Pages/Regulations/Partials/InstanceDialog.vue'));
const dialogHeader = computed(() => {
    const types: Record<string, string> = {
        node: 'carpeta',
        definition: 'definición de límites'
    };

    if (tree.nodeAction.value) {
        return {
            creating: `Crear ${types[tree.newNodeType.value ?? '']}`,
            editing: `Editar ${types[tree.selection.value?.type ?? '']}`
        }[tree.nodeAction.value];
    }
});

onMounted(() => tree.nodes.value = regulation.value.instances);

function addParameter() {
    const parameter = unref(parameterToAdd);

    if (!parameter) {
        toast.add({
            detail: 'Seleccione un parámetro para agregar',
            severity: 'warn',
            life: 3000
        });

        return;
    }

    axios.post(route('regulations.parameters', regulation.value.id), {
        parameter_id: parameterToAdd.value
    })
    .then((response) => {
        regulation.value.parameters.push(response.data);
        toast.add({
            detail: `Se agregó ${response.data.name} ${response.data.area} a la regulación.`,
            severity: 'success',
            life: 3000
        });
    })
    .catch((error) => {
        if (error.response) {
            toast.add({
                detail: error.response.data.message,
                severity: 'warn',
                life: 3000
            })
        }
    });
}

const loadChildren = (node: TreeNode) => axios.get(route('regulations.instances.nodes.children', node.key));

onMounted(async () => {
    const response = await axios.get(route('matrices.parameters', regulation.value.matrix.id));
    matrixParameters.value = response.data;
});

function validateInsertion(): string | null {
    if (tree.selection.value?.type === 'definition') {
        return 'No se pueden agregar más elementos';
    }

    return null;
}

function saveObservation() {
    axios.patch(route('regulations.observations', regulation.value.id), { text: regulation.value.observation })
}

const onOpenDialog = () => dialog.open(dialogComponent, {
    props: {
        header: dialogHeader.value,
        modal: true,
        draggable: false
    }
});

function onNodeDelete() {
    const currentNode = tree.selection.value?.label;
    axios.delete(route('regulations.instances.nodes.destroy', tree.selection.value?.key))
        .then(() => {
            tree.isNodeDeleted.value = true;
            toast.add({
                detail: `Se borró ${currentNode}`,
                life: 3000
            });
            nextTick(() => tree.isNodeDeleted.value = false)
                .then(() => tree.clearSelection());
        });
}
</script>

<template>
    <div class="h-1/7 pb-3">
        <Card pt:root="h-full">
            <template #content>
                <h2 class="font-bold text-3xl p-2">{{ selection?.label }}</h2>
                <div class="px-2 bg-blue-500 text-slate-100 text-xl inline-block rounded-sm border-blue-400 self-start">
                    {{ regulation.matrix.name }}
                </div>
            </template>
        </Card>
    </div>
    <div class="flex gap-3 h-6/7">
        <Card pt:root="w-1/3" pt:content="flex flex-col gap-5">
            <template #content>
                <Select
                    v-model="parameterToAdd"
                    option-label="name"
                    option-value="id"
                    placeholder="Selecciona un parámetro"
                    filter
                    :options="matrixParameters">
                    <template #option="slotProps">
                        <div class="flex justify-between w-full">
                            <div class="over-flow-hidden text-wrap max-w-72">{{ slotProps.option.name }}</div>
                            <div class="text-neutral-400">{{ slotProps.option.area }}</div>
                        </div>
                    </template>
                </Select>
                <Button label="Agregar parámetro" @click="addParameter"/>

                <Divider/>
                <TreeNodeToolbar tree-id="instance_tree"
                    :node-types="[
                        { type: 'node', label: 'Carpeta', icon: 'fa-solid fa-folder' },
                        { type: 'definition', label: 'Definición', icon: 'fa-solid fa-scale-balanced' }
                    ]"
                    :validate-insertion
                    @open-dialog="onOpenDialog"
                    @node-delete="onNodeDelete"/>
                <TreeNavigator
                    tree-id="instance_tree"
                    :load-children/>
            </template>
        </Card>
        <div class="w-2/3 flex flex-col gap-3">
            <Card
                pt:root="h-3/5"
                pt:body="overflow-hidden"
                pt:content="overflow-hidden">
                <template #content>
                    <RegulatoryThresholds/>
                </template>
            </Card>
            <Card pt:root="h-2/5">
                <template #title>Observación soporte técnico</template>
                <template #content>
                    <Textarea v-model="regulation.observation" rows="5" class="resize-none w-full"/>
                </template>
                <template #footer>
                    <Button label="Guardar observación" @click="saveObservation"/>
                </template>
            </Card>
        </div>
    </div>
</template>