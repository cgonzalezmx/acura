import { Sample } from "@/types/sample";
import { Take } from "@/types/take";

export default class implements Partial<Sample> {
    public id?: number | undefined;
    public identifier?: string | undefined;
    public observation?: string | null | undefined;
    public sample_temperature?: number | undefined;
    public total_containers?: number | undefined;
    public refrigerator?: string | undefined;
    public sampling_point?: string | undefined;
    public takes?: Take[] | undefined;
    public reception_date?: Date | undefined;
    public sampled_by?: number | undefined;
    public sampling_format_id?: number | undefined;

    constructor(data: Partial<Sample> & { matrix?: string }) {
        this.id = data?.id;
        this.identifier = data?.identifier;
        this.observation = data?.observation ?? '';
        this.sample_temperature = data?.sample_temperature ?? 0;
        this.total_containers = data?.total_containers ?? 0;
        this.refrigerator = this.refrigeratorFor(String(data.matrix));
        this.sampling_point = data?.sampling_point ?? '';
        this.takes = data.takes;
        this.reception_date = new Date(data.reception_date ?? Date.now());
        this.sampled_by = data.sampled_by;
        this.sampling_format_id = data.sampling_format_id;
    }

    private refrigeratorFor(matrix: string) {
        return {
            M1: 'R-123',
        }[matrix];
    }

    public newSample(data: Partial<Sample> & { matrix: string }) {
        this.observation = '';
        this.sample_temperature = 0;
        this.total_containers = 0;
        this.refrigerator = this.refrigeratorFor(data.matrix);
        this.sampling_point = data?.sampling_point ?? '';
        this.takes = data.takes;
        this.reception_date = new Date();
        this.sampled_by = data.sampled_by;
        this.sampling_format_id = data.sampling_format_id;
    }
}
