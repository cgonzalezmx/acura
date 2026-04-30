interface User {
    id: number;
    name: string;
    alias: string;
    roles: Role[];
    signature_url: string;
    permissions: any[]
}