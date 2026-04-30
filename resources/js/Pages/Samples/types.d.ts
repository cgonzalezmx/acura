import { PageProps } from "@/types";
import { Sample } from "@/types/sample";

export interface EntityDetails {
    name: string;
    address: string;
    contact_name: string;
    contact_phone: string;
    contact_email: string;
}

export interface Format {
    id: number;
    identifier: string;
}

export interface Matrix {
    name: string;
    code: string;
}

export interface SampleOverview {
    client: EntityDetails;
    site: EntityDetails;
    takes_count: number;
    takes?: Take[];
    delivered_by_client: boolean;
    format: Format;
    points: string;
    matrix: string;
    is_urgent: boolean;
    sample_type: string;
    sample: Sample | Partial<Sample>;
}

export interface Sampler {
    id: number;
    name: string;
}

export type TPageProps = PageProps & {
    samplers: Sampler[],
    samples: Sample[]
}