export function cloneMap<K, V>(inputMap: Map<K, V>) {
    return new Map(Array.from(inputMap.entries()).map(([k, v]) => [k, structuredClone(v)]));
}