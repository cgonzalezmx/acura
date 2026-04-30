export interface Parameter {
    id: number;
    name: string;
    price: number;
    unit_volume: string;
    group_volume: string;
    parameter_category_id: number;
    lab_matrix_id: number;
    analysis_area_id: number;
    methodology_id: number;
    measurement_unit_id: number;
    sample_container_id: number;
    sample_preserver_id: number;
    sample_storage_id: number;
    label_color_id: number;
    quote_remarks: {
        id: number;
        code: string;
        description: string;
    }[];
    sampling_remarks: {
        id: number;
        code: string;
        description: string;
    }[];
    multiple: boolean;
}