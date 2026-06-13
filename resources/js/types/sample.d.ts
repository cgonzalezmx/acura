export interface Sample {
    id: number;
    identifier: string;
    observation: string | null;
    sample_temperature: number;
    total_containers: number;
    refrigerator: string;
    sampling_point: string;
    takes: Take[];
    reception_date: Date;
    sampled_by: number;
    sampling_format_id: number;
    [key:string]: any;
}
