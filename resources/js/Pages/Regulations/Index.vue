<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import TreeNavigator from '@/Components/Tree/TreeNavigator.vue';
import TreeNodeToolbar from '@/Components/Tree/TreeNodeToolbar.vue';
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card';
import { TreeNode } from 'primevue/treenode';
import { useTree } from '@/Components/Tree/useTree';
import { RegulationNodeType as NodeType } from './types';
import { useDialog } from 'primevue/usedialog';
import { computed, defineAsyncComponent, nextTick, onMounted, unref } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

interface Props {
    nodes: TreeNode[];
}

const treeId = 'reg_tree';
defineOptions({ layout: HomeLayout });
const { nodes } = defineProps<Props>();
const tree = useTree(treeId);
const dialog = useDialog();
const toast = useToast();
const dialogComponent = defineAsyncComponent(() => import('@/Pages/Regulations/Partials/NodeDialog.vue'))
const dialogHeader = computed(() => {
    const types: Record<string, string> = {
        node: 'carpeta',
        regulation: 'regulación',
        bundle: 'paquete'
    };

    if (tree.nodeAction.value) {
        return {
            creating: `Crear ${types[tree.newNodeType.value ?? '']}`,
            editing: `Editar ${types[tree.selection.value?.type ?? '']}`
        }[tree.nodeAction.value];
    }
});

const showOverview = computed(
    () => 
        tree.selection.value !== null
        && tree.selection.value?.type !== 'node'
);

const overviewComponent = computed(() => {
    const selection = unref(tree.selection);
    if (selection !== null) {
        return {
            bundle: defineAsyncComponent(() => import('./Partials/BundleOverview.vue')),
            regulation: defineAsyncComponent(() => import('./Partials/RegulationOverview.vue'))
        }[selection.type as 'bundle' | 'regulation'];
    }
});

onMounted(() => tree.nodes.value = [ ...nodes ]);

function validateInsertion(nodeType: string): string | null {
    const canAddNodeToRoot = (type: string) => type === 'node';
    const canAddToBundleParent = (parentType: string) => parentType !== 'bundle';
    const hasRegulationInRoute = (treeRoute: NodeType[]) => treeRoute.some((item) => item === 'regulation');
    const selection =  tree.selection;

    if (!selection.value) {
        if(!canAddNodeToRoot(nodeType)) {
            return 'No se puede agregar este elemento a la raíz.';
        }
    }
    else {
        const parentType = selection.value.type as NodeType;

        if (!canAddToBundleParent(parentType)) {
            return 'No se pueden agregar más elememtos.';
        }

        const regulationInRoute = hasRegulationInRoute(tree.pathToSelection.value.map((node) => node.type as NodeType));

        if (nodeType === 'bundle' && !regulationInRoute) {
            return 'La ruta debe tener una regulación definida.';
        }

        if (nodeType === 'regulation' && regulationInRoute) {
            return 'La ruta no puede tener más de una regulación.';
        }
    }
    
    return null;
}

const loadChildren = (node: TreeNode) => axios.get(route('regulations.nodes.edit.children', node.key));
const onOpenDialog = () => dialog.open(dialogComponent, {
    props: {
        header: dialogHeader.value,
        modal: true,
        draggable: false
    }
});

function onNodeDelete() {
    const currentNode = tree.selection.value?.label;
    axios.delete(route('regulations.nodes.destroy', tree.selection.value?.key))
        .then(() => {
            tree.isNodeDeleted.value = true;
            toast.add({
                detail: `Se borró ${currentNode}`,
                life: 3000
            });
            nextTick(() => tree.isNodeDeleted.value = false)
                .then(() => tree.clearSelection);
        })
}
</script>

<template>
    <Head title="Regulaciones"/>
    <div class="p-6 flex flex-col lg:flex-row gap-3 h-full">
        <Card title="Árbol" class="lg:w-1/2 xl:w-1/3">
            <template #title>Regulaciones</template>
            <template #content>
                <TreeNodeToolbar
                    :tree-id :node-types="[
                        { type: 'node', label: 'Carpeta', icon: 'fa-solid fa-folder' },
                        { type: 'regulation', label: 'Regulación', icon: 'fa-solid fa-rectangle-list' },
                        { type: 'bundle', label: 'Paquete', icon: 'fa-solid fa-cube' }
                    ]"
                    :validateInsertion
                    @open-dialog="onOpenDialog"
                    @node-delete="onNodeDelete"
                />
                <TreeNavigator :tree-id :load-children/>
            </template>
        </Card>
        <div v-if="showOverview" class="lg:w-1/2 xl:w-2/3">
            <component :is="overviewComponent"/>
        </div>
    </div>
</template>