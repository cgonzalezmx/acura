import { getRouteFromLeaf } from "@/utils/tree";
import { TreeNode } from "primevue/treenode";
import { computed, ComputedRef, reactive, Ref, ref, shallowRef, ShallowRef, watch } from "vue";

export type NodeAction = 'creating' | 'editing';

interface StateProps {
    selection: TreeNode | null;
    selectedKeys: Record<number, boolean>;
    expandedKeys: Record<number, boolean>;
}

export interface TreeInstance {
    id: string;
    nodes: Ref<TreeNode[]>;
    selection: ShallowRef<TreeNode | null>;
    select: (node: TreeNode) => void;
    clearSelection: () => void;
    hasSelection: ComputedRef<boolean>;
    expandedKeys: ShallowRef<Record<string, boolean>>;
    selectedKeys: ShallowRef<Record<string, boolean>>;
    isLoading: Ref<boolean>;
    nodeAction: Ref<NodeAction | null>;
    newNodeType: Ref<string | null>;
    startCreating: (type: string) => void;
    cancelAction: () => void;
    append: (node: TreeNode) => void;
    isNodeDeleted: Ref<boolean>;
    pathToSelection: ComputedRef<TreeNode[]>
    deleteInstance: () => void;
    onSelectionChange: (callback: (node: TreeNode | null) => void) => void
    loadState: (state: StateProps) => void
}

const treeRegistry = new Map<string, TreeInstance>();

export function deleteTree(id: string) {
    treeRegistry.delete(id)
    return;
}

export function useTree(id: string): TreeInstance {
    if (treeRegistry.has(id)) {
        return treeRegistry.get(id) as TreeInstance;
    }

    const nodes = shallowRef<TreeNode[]>([]);
    const selection = shallowRef<TreeNode | null>(null);
    const hasSelection = computed(() => selection.value !== null);
    const expandedKeys = shallowRef<Record<string, boolean>>({});
    const selectedKeys = shallowRef<Record<string, boolean>>({});
    const isLoading = ref(false);
    const nodeAction = ref<NodeAction | null>(null);
    const newNodeType = ref<string | null>(null);
    const clearSelection = () => selection.value = null;
    const select = (node: TreeNode) => selection.value = node;
    const isNodeDeleted = ref(false);
    const selectionListeners = new Set<(node: TreeNode | null) => void>();

    const pathToSelection = computed(() => {
        const selectedKey = Object.keys(selectedKeys.value)[0];
        if (selectedKey) {
            const path = getRouteFromLeaf(nodes.value, selectedKey);
            return path;
        }

        return [];
    });

    function startCreating(type: string) {
        newNodeType.value = type;
        nodeAction.value = 'creating';
    }
    
    function cancelAction() {
        newNodeType.value = null;
        nodeAction.value = null;
    }

    function append(node: TreeNode) {
        if (!selection.value) {
            nodes.value = [ ...nodes.value, reactive(node) ]
            return;
        }
        else {
            if (!Array.isArray(selection.value.children)) {
                selection.value.children = [];
            }

            selection.value.children.push(node);
            selection.value.leaf = false;
        }
    }

    const deleteInstance = () => {
        treeRegistry.delete(id);
        selectionListeners.clear();
    }

    function onSelectionChange(callback: (node: TreeNode | null) => void) {
        selectionListeners.add(callback);
    }

    function loadState(state: StateProps) {
        selection.value = state.selection
        expandedKeys.value = state.expandedKeys;
        selectedKeys.value = state.selectedKeys
    }

    watch(selection, (sel) => {
        for (const listener of selectionListeners) {
            listener(sel);
        } 
    });

    const instance: TreeInstance = {
        id,
        nodes,
        selection,
        hasSelection,
        expandedKeys,
        selectedKeys,
        isLoading,
        nodeAction,
        newNodeType,
        isNodeDeleted,
        pathToSelection,
        select,
        clearSelection,
        startCreating,
        cancelAction,
        append,
        deleteInstance,
        onSelectionChange,
        loadState
    };

    treeRegistry.set(id, instance);
    return instance;
}