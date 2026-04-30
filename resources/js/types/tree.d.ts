import { TreeNode } from "primevue/treenode";

export type RegulationTreeNodeType = 'node' | 'regulation' | 'bundle';

export interface RegulationTreeNode extends TreeNode {
    id: number;
    name: string;
    type: RegulationTreeNodeType;
    parent_id?: number;
    children?: Node[];
    leaf: boolean;
}
