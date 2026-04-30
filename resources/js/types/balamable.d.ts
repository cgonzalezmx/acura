import { User } from '@/types';

export interface Blamable {
    created_at?: string;
    created_by?: number | User;
    updated_at?: string;
    updated_by?: number | User;
    deleted_at?: string;
    deleted_by?: number | User;
    version?: number;
}