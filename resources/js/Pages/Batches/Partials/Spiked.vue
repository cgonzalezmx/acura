<script setup lang="ts">
import InputNumber from 'primevue/inputnumber';
import { roundNumber } from '@/utils/number';
import { computed, ref } from 'vue';
import { useProcedureStore } from '../store';
import { storeToRefs } from 'pinia';

const store = useProcedureStore();
const { config } = store;
const { controls, references } = storeToRefs(store);

if (!controls.value?.spiked?.data) {
    controls.value!.spiked.data = {
        sampleAliquot: 0,
        spikeStockConc: 0,
        spikeVolAdded: 0,
    };
}

const massMeasured = computed(() => {
    const spiked = controls.value?.spiked;

    if (!spiked || spiked.result === 0) return 0;

    return spiked.result * (spiked.params?.quantity ?? 0);
});
const massNative = computed(() => {
    const sampleAliquot = controls.value!.spiked.data!.sampleAliquot;
    return references.value.spiked * sampleAliquot;
});
const massAdded = computed(() => {
    const { spiked } = controls.value!;
    const spikeStockConc = spiked.data!.spikeStockConc;
    const spikeVolAdded = spiked.data!.spikeVolAdded;

    return spikeStockConc * spikeVolAdded;
});
const percentage = computed(() => {
    if (massAdded.value === 0) return 0;
    const result = ((massMeasured.value - massNative.value) / massAdded.value) * 100;
    return Math.abs(roundNumber(result, { decimals: 2 }));
});

const maxFractionDigits = 4;
</script>

<template>
    <div v-if="config?.controls?.spiked" class="grid grid-cols-2 gap-2 bg-slate-100 border border-slate-200 p-4 rounded-md">
        <div>
            <div class="font-semibold">Porcentaje de aceptación de fortificado (%).</div>
            <div class="grid grid-cols-[auto_150px] gap-2">
                <div class="grid grid-cols-[auto_auto] gap-2">
                    <div>Volumen de muestra para fortificado.</div>
                    <InputNumber v-model="controls!.spiked.data!.sampleAliquot" size="small" :max-fraction-digits fluid class="font-mono"/>
                    <div>Concentración teórica de la solución patrón.</div>
                    <InputNumber v-model="controls!.spiked!.data!.spikeStockConc" size="small" :max-fraction-digits fluid class="font-mono"/>
                    <div>Volumen tomado de la solución patrón</div>
                    <InputNumber v-model="controls!.spiked.data!.spikeVolAdded" size="small" :max-fraction-digits fluid class="font-mono"/>
                </div>
                <div class="flex items-center">
                    <div>RF% = <span class="font-mono">{{ percentage }}</span></div>
                </div>
            </div>
        </div>
        <div></div>
    </div>
    <div v-else>El análisis no está configurado para medir el porcentaje de aceptación de fortificado.</div>
</template>

<style scoped>
</style>
