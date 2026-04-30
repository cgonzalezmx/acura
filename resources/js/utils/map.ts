import { toRaw } from "vue";

type MapKey = string | number;

export function makeNewMap<K extends MapKey, V>(input: Map<K, V> | Record<K, V> | null | undefined): Map<K, V> {
    if (input instanceof Map) {
        console.log(input);
        return new Map(Array.from(input.entries()).map(([k, v]) => [k, structuredClone(toRaw(v)) as V]));
    }

    if (typeof input === 'object' && input !== null) {
        return new Map(Object.entries(input).map(([k, v]) => [Number(k) as K, v as V]));
    }

    return new Map<K, V>();
}