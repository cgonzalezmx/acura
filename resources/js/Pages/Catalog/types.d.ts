export interface Field {
    label: string;
    item: string;
    labelIdentifier: string;
    type: 'string' | 'text'
}

export interface Warning {
    label: string;
    field: string;
}

export interface ResourceConfig {
    route: string;
    only: string[];
}