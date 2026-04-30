import * as App from "@/types";
import { Component, Ref, ShallowRef } from "vue";

export interface ExtendedAttributes {
    [key:string]: {
        header: string;
        value: any;
    }
}

export interface Batch {
    id: number;
    name: string;
    parameter: string;
    authorized: boolean;
    checkin_time: Date | null;
    checkout_time: Date | null;
    analyzed_at: Date | null;
    log: string;
    solutions_log: string;
    range: 'mid' | 'low' | 'high';
    minimal_cuantification: string;
    params?: Record<string, any>;
    analysis_count: number;
    sample_storages: any[];
    analysis_area: {
        id: number;
        code: string;
        name: string;
    },
    measurement_unit: any;
    matrix: string;
    controls?: Record<string, Control | AnalysisLikeControl>;
}

export type Procedure = Omit<Batch, 'id' | 'parameter' | 'range' | 'minimal_cuantification' | 'analysis_count' | 'analysis_area' | 'authorized' | 'min_quantifiable' | 'measurement_unit' | 'params' | 'controls' | 'matrix'>;

export interface Analysis {
    id: number;
    index: number;
    result: any;
    reported_result: any;
    registered: boolean;
    authorized: boolean;
    params?: Record<string, Ref<any>>;
    threshold: any;
    sample: any;
    [key: string]: any;
}

export type PageProps = App.PageProps & {
    batch: Batch;
    analyses: Analysis[];
    refrigerators: Array<{
        id: number;
        identifier: string;
    }>;
    min_quantifiable: any;
};

export interface Control {
    type: string;
    label: string;
    enabled: boolean;
    [key: string]: any;
    has_data?: true;
    data?: Record<string, any>;
}

export interface AnalysisLikeControl extends Control {
    needs_reference?: true;
    reference_id?: number;
    params?: any;
    result: any;
    reported_result: any;
}

export interface ColumnConfig {
    key: string;
    inputType: 'number' | 'text' | 'date' | 'info' | 'custom';
    header: string;
    defaultValue?: any;
    toggable?: true;
    component?: Component;
    omitFromControls?: true;
    decimals?: number;
};

export interface Specification {
}

export interface SetupProps {
    analyses: ShallowRef<Analysis[]>;
    params?: Record<string, any>;
    controls?: Ref<Record<string, Control | AnalysisLikeControl> | undefined>;
}

export interface Config {
    extendedColumns?: ColumnConfig[];
    result?: {
        header: string;
        isReadOnly: boolean;
        decimals?: number;
    },
    rowsPerAnalysis?: number;
    extendedAttributes?: ExtendedAttributes;
    controls?: Record<string, Control | AnalysisLikeControl>;
    setup?: (setupProps: SetupProps) => void;
    veredictClass?: (analysis: Analysis) => string;
    specifications?: Specification[];
}
