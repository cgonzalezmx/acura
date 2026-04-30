export interface Take {
    id?: number;
    timestamp: Date | string | null;
    color: string;
    odour: string;
    appearance: string;
    [key:string]: any;
}