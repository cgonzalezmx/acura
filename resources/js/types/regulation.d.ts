import { Blamable } from "@/types/balamable";

export interface Regulation extends Blamable {
    id: number;
    lab_matrix_id: number;
}

export interface Bundle extends Blamable {
    id: number;
    price: number;
    takes: number;
    regulation_id?: number;
    regulation?: Regulation;
}