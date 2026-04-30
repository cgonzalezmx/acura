<script setup lang="ts">
import Tree, { TreePassThroughOptions } from 'primevue/tree';
import { useTree } from './useTree';
import { TreeNode } from 'primevue/treenode';
import { AxiosPromise } from 'axios';

interface Props {
    treeId: string;
    loadChildren?: (node: TreeNode) => AxiosPromise
}

interface Events {
    nodeExpand: [node: TreeNode];
    nodeSelect: [node: TreeNode];
}

const emit = defineEmits<Events>();
const { treeId, loadChildren } = defineProps<Props>();
const { nodes, select, clearSelection, expandedKeys, selectedKeys, isLoading } = useTree(treeId);

const treePassThroughOptions: TreePassThroughOptions = {
    nodeIcon({ context }) {
        if (context.node.type === 'node') {
            return `fa-solid ${context.expanded ? 'fa-folder-open' : 'fa-folder'}`;
        }
    }
};

async function onNodeExpand(node: TreeNode) {
    emit('nodeExpand', node);
    if (node.children) return;

    const tree = useTree(treeId);
    //tree.isLoading.value = true;
    node.loading = true;
    const response = await loadChildren?.(node)
    node.children = response?.data;
    node.loading = false;
    //tree.isLoading.value = false;
}

function onNodeSelect(node: TreeNode) {
    select(node);
    emit('nodeSelect', node);
}
</script>

<template>
    <Tree
        v-model:selection-keys="selectedKeys"
        v-model:expanded-keys="expandedKeys"
        selection-mode="single"
        loading-mode="icon"
        filter
        :value="nodes"
        :pt="treePassThroughOptions"
        @node-expand="onNodeExpand"
        @node-select="onNodeSelect"
        @node-unselect="clearSelection"
        class="bg-white">
        <template #default="{node}">
            {{ node.label }} <span v-if="node.alias" class="text-surface-400 font-light text-xs">{{ node.alias }}</span>
        </template>
    </Tree>
</template>