import { PageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { TreeNode } from 'primevue/treenode';
import { ref } from 'vue';

type LocalPageProps = PageProps<{
    root_nodes: TreeNode[]
}>;

const rootNodes = ref<TreeNode[]>([]);

export function useRootNodes() {
    rootNodes.value = usePage<LocalPageProps>().props.root_nodes;
    return rootNodes;
}