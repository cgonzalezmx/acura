<script setup lang="ts">
import { roundNumber } from '@/utils/number';
import { useProcedureStore } from '../store';
import { computed, unref } from 'vue';

const store = useProcedureStore();
const { config } = store;

const percentage = computed(() => {
    if (!store.references?.duplicate) return 0;

    const duplicate = roundNumber(unref(store.controls?.duplicate?.result) ?? 0, { decimals: 2 });
    const sample = roundNumber(unref(store.references?.duplicate) ?? 0, { decimals: 2 });
    const result = ((duplicate - sample) / (duplicate + sample)) * 200;

    return Math.abs(roundNumber(result, { decimals: 2 }));
});
</script>

<template>
    <div v-if="config?.controls?.duplicate" class="grid grid-cols-2 gap-2 bg-slate-100 border border-slate-200 p-4 rounded-md">
        <div>
            <div class="font-semibold">Porcentaje de desviación relativo (%)</div>
            <div>%RD = <span class="font-mono">{{ percentage }}</span></div>
        </div>
        <div></div>
    </div>
    <div v-else>
        El análisis no está configurado para medir el porcentaje de desviación relativo.
    </div>
</template>

<style scoped>

</style>
