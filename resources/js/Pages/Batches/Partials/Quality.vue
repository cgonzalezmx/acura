<script setup lang="ts">
import InputNumber from 'primevue/inputnumber';
import { useProcedureStore } from '../store';
import { computed, ref } from 'vue';
import { roundNumber } from '@/utils/number';

const store = useProcedureStore();
const { config } = store;

if (!store.controls?.quality.data) {
    store.controls!.quality.data = { value: 0 };
}

const input = computed({
    set(v: number) {
        store.controls!.quality.data!.value = v;
    },
    get() {
        return store.controls!.quality.data!.value
    }
});
const percentage = computed(() => {
    if (store.controls?.quality) {
        if (!input.value) return 0;
        const value = (store.controls.quality.result / input.value) * 100;
        return Math.abs(roundNumber(value, { decimals: 1 }));
    }

    return 0;
});
</script>

<template>
    <div v-if="config?.controls?.quality" class="grid grid-cols-2 gap-2 bg-slate-100 border border-slate-200 p-4 rounded-md">
        <div>
            <div class="font-semibold">Porcentaje de recuperación de control de calidad (%)</div>
            <div class="grid grid-cols-2 gap-2">
                <div class="flex">
                    <div class="grow">Concentración teórica</div> <InputNumber v-model="input" size="small" :max-fraction-digits="4" fluid class="w-24 font-mono"/>
                </div>
                <div>%R = <span class="font-mono">{{ percentage }}</span></div>
            </div>
        </div>
        <div></div>
    </div>
    <div v-else>
        El análisis no está configurado para medir el porcentaje de recuperación.
    </div>
</template>

<style scoped>

</style>
