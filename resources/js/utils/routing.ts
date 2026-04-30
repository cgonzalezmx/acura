type StandardMethod = 'index' | 'create' | 'store' | 'show' | 'edit' | 'update' | 'destroy';
type RouteConfig =
    | StandardMethod
    | { name: string, as?: string, params?: Record<string, any>};

function validate(action: string, hasParams: boolean = false) {
    const needId: (StandardMethod | string)[] = ['show', 'edit', 'update', 'destroy'];

    if (needId.includes(action) && !hasParams) {
        throw new Error(`La acción ${action} reqiere identificador del recurso`);
    }
}

export function resource(
    base: string,
    configs: RouteConfig | RouteConfig[]
): Record<StandardMethod | string, string> {
    const routes: Record<string, string> = {};
    const configArray = Array.isArray(configs) ? configs : [configs];

    let key: string;
    let routeName: string;
    let params: Record<string, any> = {};

    for (const config of configArray) {
        if (typeof config === 'string') {
            validate(config);
            key = config;
            routeName = `${base}.${config}`
        } else {
            validate(config.name, Object.keys(config.params ?? {}).length > 0);
            key = config.as || config.name,
            routeName = `${base}.${config.name}`,
            params = config.params || {};
        }

        routes[key] = route(routeName, params);
    }

    return routes;
}