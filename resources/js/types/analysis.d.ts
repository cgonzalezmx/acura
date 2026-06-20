import type { Threshold as QThreshold } from "@/Pages/Quotes/types";
import { Parameter } from "./parameter";

interface ComplementaryThresholdData {
    passed: boolean;
    enabled: boolean;
    letter: string;
    sample_id: number;
}

export type Threshold = Omit<QThreshold, 'custom_boundary'> & Omit<ComplementaryThresholdData, 'sample_id'>;

export interface Analysis {
    id: number;
    batch_id: number;
    parameter_id: number;
    analized_at: string;
    authorized: boolean;
    authorized_at: string;
    authorized_by: any;
    index: number;
    lab_matrix_id: number;
    log: string;
    minimal_quantification: string;
    parameter_id: number;
    parameter?: Parameter;
    params: any;
    range: 'low' | 'mid' | 'high';
    registered: boolean;
    registration_counter: number;
    reported_by: any;
    reported_result: string;
    result: string;
    sample_id: any;
    saved_at: string;
    take_id: number;
    thresholds: Threshold[];
    uncertainty: string;
    veredict: string;
}
