import { Take } from "@/types/take";

export default class implements Take {
    public id?: number | undefined;
    public timestamp: Date | string | null;
    public color: string;
    public odour: string;
    public appearance: string;

    constructor(data: Take | null = null) {
        this.id = data?.id;
        this.timestamp = new Date(data?.timestamp ?? Date.now());
        this.color = data?.color ?? '';
        this.odour = data?.odour ?? '';
        this.appearance = data?.appearance ?? '';
    }

    static empty() {
        return new this();
    }
}