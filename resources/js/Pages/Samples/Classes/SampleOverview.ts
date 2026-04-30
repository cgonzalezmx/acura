import Sample from "./Sample";
import { SampleOverview } from "../types";
import { EntityDetails } from "../types";
import { Format } from "../types";

export default class implements SampleOverview {
    public client: EntityDetails;
    public site: EntityDetails;
    public takes_count: number;
    public delivered_by_client: boolean;
    public format: Format;
    public points: string;
    public is_urgent: boolean;
    public matrix: string;
    public sample_type: string;
    public sample: Sample;

    constructor(data: Record<keyof SampleOverview, any>) {
        const sample = data.sample;
        this.client = data.client;
        this.site = data.site;
        this.takes_count = data.takes_count;
        this.delivered_by_client = data.delivered_by_client;
        this.format = data.format;
        this.points = data.points;
        this.is_urgent = data.is_urgent;
        this.matrix = data.matrix;
        this.sample_type = data.sample_type;
        this.sample = new Sample({
            ...sample,
            takes: sample?.takes ?? null,
            sampling_format_id: this.format.id,
            matrix: this.matrix,
            sampling_point: sample?.sampling_point ?? this.points
        });
    }
}