import { TreeNode } from "primevue/treenode";

export type RegulationNodeType = 'node' | 'regulation' | 'bundle';

export interface RegulationNode extends TreeNode {
    id: number;
    type: RegulationNodeType
}