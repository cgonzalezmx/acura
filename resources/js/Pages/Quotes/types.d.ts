import { TreeInstance } from "@/Components/Tree/useTree";
import { TreeNode } from "primevue/treenode";
import { ComputedRef, Ref, ShallowReactive, ShallowRef } from "vue";

export type IncludedParameters = Record<number, {
    parameter_id?: number,
    quantity: number;
    expected_quantity: number;
    price?: number;
    from_system: boolean;
    from_main_report: boolean;
}>;

export interface Parameter {
    parameter_id: number;
    id?: number;
    name: string;
    area_id: number;
    price: number;
    methodology: string;
    version: number;
    multiple: boolean;
}

export interface Bundle {
    id: number;
    price: number;
    takes: number;
    parameters: Array<{ id: number }>
}

export interface Threshold {
    id?: number;
    parameter_id: number;
    min: string | undefined;
    max: string;
    custom_boundary: 'min' | 'max' | 'both' | 'none';
}

export interface Report {
    id: number | null;
    report_id: string;
    structure: TreeInstance;
    instance: TreeInstance;
    is_main_report: boolean;
    parameters: ShallowReactive<Map<number, {
        quantity: number,
        from_system: boolean;
        from_main_report?: boolean;
    }>>;
    thresholds: {
        system: ShallowReactive<Map<number, Threshold>>;
        custom: Reactive<Map<number, Threshold>>;
    };
    observation: Ref<string>;
}

export interface Entry {
    id: number | null;
    entry_id: string;
    title: string;
    is_urgent: boolean;
    form_factor: string;
    result_time_lapse: number;
    objective: string;
    takes: number;
    sampling_date: Date;
    sample_container_type: string;
    total_containers: number;
    total_volume: string;
    sample_temperature: number;
    refrigerated: boolean;
    observation: string;
    sample_type: string;
    sample_reception_date: Date;
    concept: string;
    bundle_price: number;
    extras: number;
    price_offset: number;
    total_cost: number;
    matrix_id: number;
    reports: ShallowReactive<Record<string, Report>>;
    quantityOverrides: ShallowReactive<Record<number, number>>
    included_parameters: IncludedParameters;
    quantity: number;
    price_offset_notes: string | null;
    is_loaded?: boolean;
}

export interface Balance {
    subtotal: number;
    priceAdjustment: number;
    iva: number;
}

export interface Expense {
    id: number | null;
    concept: string;
    cost: number;
    quantity: number;
}
