import { isRef, Ref } from "vue";

/**
 * Comprueba si el valor de un ref es igual al de una variable u otro ref
 * @param ref
 * @param value
 * @returns boolean
 */
export function eq<T>(ref: Ref<T>, value: Ref<T> | T): boolean {
    if (isRef(value)) {
        return ref.value === value.value;
    }

    return ref.value === value;
}