<script setup lang="ts">
import { MenuItem } from 'primevue/menuitem';
import { useToast } from 'primevue/usetoast';
import Menubar from 'primevue/menubar';
import { useTree } from './useTree';
import { watch } from 'vue';

interface Props {
    treeId: string;
    nodeTypes: Array<{
        type: string;
        label: string;
        icon: string;
    }>;
    validateInsertion?: (nodeType: string) => string | null;
}

interface Events {
    openDialog: [],
    nodeDelete: []
}

const { treeId, nodeTypes, validateInsertion } = defineProps<Props>();
const { startCreating, selection, expandedKeys, isNodeDeleted, pathToSelection, nodeAction } = useTree(treeId);
const emit = defineEmits<Events>();
const toast = useToast();

watch(isNodeDeleted, (triggered) => {
    if (!selection.value) return;
    const parent = pathToSelection.value.at(-2);
    if (typeof parent?.children === 'undefined') return;

    if (triggered) {
        const index = parent.children.findIndex((child) => child.key === selection.value?.key);

        if (typeof index === 'number') {
            parent.children.splice(index, 1);
        }

        if (parent.children.length === 0) {
            parent.leaf = true;
            delete expandedKeys.value[parent.key];
        }
    }
});

const menuItems: MenuItem[] = [
    {
        label: 'Nuevo',
        icon: 'fa-solid fa-plus',
        items: nodeTypes.map((item) => ({
            label: item.label,
            icon: item.icon,
            command: () => initiateNodeCreation(item.type)
        }))
    },
    {
        label: 'Editar',
        icon: 'fa-solid fa-pen',
        command: initiateNodeEdition
    },
    {
        label: 'Borrar',
        icon: 'fa-solid fa-trash-can',
        command: () => emit('nodeDelete')
    }
];

const createWarning = (detail: string) => toast.add({
    detail,
    severity: 'warn',
    life: 3000
});

function initiateNodeCreation(type: string) {
    const error = validateInsertion?.(type);

    if (error) {
        createWarning(error);
        return;
    }

    startCreating(type);
    emit('openDialog');
}

function initiateNodeEdition() {
    if (!selection.value) {
        createWarning('No hay ningún elemento seleccionado.');
        return;
    }

    nodeAction.value = 'editing';
    emit('openDialog');
}
</script>

<template>
    <Menubar v-bind:model="menuItems"/>
</template>