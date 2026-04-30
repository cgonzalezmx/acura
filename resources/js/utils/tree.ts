function buildMap<T>(nodes: any[], map: Map<number | string, any> = new Map()) {
    nodes.forEach((node) => {
        map.set(node.key, node);

        if (node.children && node.children.length > 0) {
            buildMap(node.children, map);
        }
    });

    return map;
}

export function getRouteFromLeaf<T>(tree: T[], leafId: number | string): T[] {
    const map = buildMap(tree);
    let treeRoute: T[] = [];
    let current = map.get(Number(leafId));

    if (!current) {
        return treeRoute;
    }

    while (current) {
        treeRoute.push(current);

        if (current.parent_id && map.get(current.parent_id)) {
            current = map.get(current.parent_id);
        }
        else {
            break;
        }
    }

    return treeRoute.reverse();
}