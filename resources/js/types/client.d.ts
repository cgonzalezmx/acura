import { Blamable } from "./balamable";
export interface Client extends Blamable {
    id?: number;
    name: string;
    company?: string;
    industry_sector?: string;
    street?: string;
    internal_number?: string;
    external_number?: string;
    neighborhood?: string;
    zip_code?: string;
    city?: string;
    state?: string;
    address?: string;
    website?: string;
}

export interface ClientContact extends Blamable {
    id: number;
    name: string;
    is_main_contact: boolean;
    phone: string;
    cellphone?: string;
    email: string;
    alt_email?: string;
    client_id: number;
    [key: string]: any;
}

export interface ClientSamplingSite extends Blamable {
    id?: number;
    name: string;
    is_main_site?: boolean;
    industry_sector?: string;
    street?: string;
    external_number?: string;
    internal_number?: string;
    neighborhood?: string;
    city?: string;
    zip_code?: string;
    state?: string;
    contact?: {
        name: string;
        phone: string;
        cellphone: string;
        email: string;
        alt_email: string;
    };
    contact_name: string;
    contact_phone: string;
    contact_cellphone: string;
    contact_email: string;
    contact_alt_email: string;
    address?: string;
    [key: string]: any;
}
