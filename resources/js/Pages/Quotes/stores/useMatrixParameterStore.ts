import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, MaybeRef, reactive, Ref, ref, shallowReactive, toRef, unref, watch } from 'vue';
import { Parameter } from '../types';

export const useMatrixParameterStore = defineStore('matrix-parameters', () => {
    const cache = shallowReactive(new Map<number, Parameter[]>())
    const getCachedMatrices = computed(() => Array.from(cache.values()));

    async function fetch(matrixId: MaybeRef<number>): Promise<Parameter[]> {
        const id = unref(matrixId);
        const { data } = await axios.get<Parameter[]>(route('matrices.parameters.output.quote', id));

        return data;
    }

    function fetchMatrix(matrixId: Ref<number>) {
        if (!cache.has(matrixId.value)) {
            watch(
                matrixId,
                async (id) => {
                    const matrixParams = await fetch(id);
                    cache.set(id, matrixParams);
                },
                { once: true }
            );
        }

        return computed(() => {
            if (cache.has(matrixId.value)) {
                return cache.get(matrixId.value) ?? [];
            }

            return [];
        });
    }

    return {
        cache,
        getCachedMatrices,
        fetchMatrix
    }
});