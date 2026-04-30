import { usePage } from "@inertiajs/vue3";

export function usePermission() {
    const user = usePage().props.auth.user;
    const permissions = user?.permissions;
    const roles = user?.roles;
    const can = (permission: string) => permissions?.contains(permission);
    const hasRole = (role: string) => roles?.conatins(role);

    return {
        permissions,
        roles,
        can,
        hasRole
    }
}