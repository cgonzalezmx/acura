import { useQuery } from '@tanstack/vue-query';
import axios from 'axios';
import { MaybeRef, unref } from 'vue';
import { Parameter } from '../types';

export function useMatrixParameters(matrixId: MaybeRef<number>) {
    return useQuery({
        queryKey: ['matrixParameters', matrixId],
        queryFn: async () => {
            const id = unref(matrixId);

            if (id > 0) {
                const reponse = await axios.get<Parameter[]>(route('matrices.parameters.output.quote', id));
                return reponse.data;
            }

            return [];
        },
        staleTime: Infinity
    });
}