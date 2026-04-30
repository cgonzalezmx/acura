<script setup lang="ts">
import { useProcedureStore } from '../store';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import { MaybeRef, Ref, unref } from 'vue';
import { useToggle } from '@vueuse/core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSquarePlus } from '@fortawesome/free-solid-svg-icons';
import { ColumnConfig } from '../types';
import { roundNumber } from '@/utils/number';

const store = useProcedureStore();
const togglables: Record<string, { bool: Ref<boolean>, toggle: (value?: boolean) => boolean}> = {};

for (const col of store.config?.extendedColumns ?? []) {
    const [bool, toggle ] = useToggle();
    if (col.toggable) togglables[col.key] = { bool, toggle };
}

function displayResult(input: MaybeRef<any>, columnConfig: ColumnConfig) {
    const result = unref(input);
    const round = () => roundNumber(result, { decimals: columnConfig.decimals ?? 2 });
    if (columnConfig.toggable) {
        return togglables[columnConfig.key].bool.value ? result : round();
    }

    return round();
}

</script>

<template>
    <div>
        <table class="w-full">
            <thead>
<tr class="text-sm">
                    <th class="header">Clave muestra</th>
                    <th v-for="col in store.config?.extendedColumns"
                        :key="col.key"
                        @click="togglables[col.key].toggle()"
                        :class="['header', { 'cursor-pointer': col.toggable }]">
                        <div v-if="col.toggable" class="flex items-center">
                            <div class="pr-2">{{ col.header }}</div>
                            <FontAwesomeIcon :icon="faSquarePlus" size="lg" :class="{ 'text-slate-300': ! togglables[col.key]?.bool.value }"/>
                        </div>
                        <span v-else>{{ col.header }}</span>
                    </th>
                    <th class="header">LMP</th>
                    <th class="header">Punto de muestreo</th>
                </tr>
            </thead>
            <tbody>
                <TransitionGroup name="controls">
                    <template v-for="con in store.controls" :key="con.type">
                        <tr v-if="con.enabled" class="border-y border-y-slate-200">
                            <td class="cell">
                                <div>{{ con.label }}</div>
                            </td>
                            <td v-for="col in store.config?.extendedColumns" :key="col.key" class="cell">
                                <div v-if="!col.omitFromControls">
                                    <InputText v-if="col.inputType === 'text'" v-model="con.params![col.key]" size="small" fluid/>
                                    <InputNumber v-if="col.inputType === 'number'" v-model="con.params![col.key]" :max-fraction-digits="5" size="small" fluid class="font-mono"/>
                                    <span v-if="col.inputType === 'info'" :class="{ 'font-mono': typeof con.params![col.key] === 'number' }">{{ displayResult(con.params![col.key], col) }}</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                </TransitionGroup>
                <template v-for="(item, index) in store.analyses" :key="item.id">
                    <tr v-for="n in store.config?.rowsPerAnalysis"
                        :key="n"
                        :class="[{'bg-slate-100': index % 2 === 0}, 'border-y border-y-slate-200']"
                    >
                        <td v-if="n === 1" :rowspan="store.config?.rowsPerAnalysis" class="cell border-r border-r-slate-200">
                            {{ item.sample.identifier }}
                        </td>
                        <td v-for="col in store.config?.extendedColumns" class="cell">
                            <InputText v-if="col.inputType === 'text'" v-model="item.params![col.key].value[n - 1]" size="small" fluid/>
                            <InputNumber v-if="col.inputType === 'number'" v-model="item.params![col.key].value[n - 1]" :max-fraction-digits="5" size="small" fluid class="font-mono"/>

                            <span v-if="col.inputType === 'info'" :class="{ 'font-mono': typeof item.params![col.key].value[n - 1].value === 'number' }">{{ displayResult(item.params![col.key].value[n - 1], col) }}</span>
                            <component v-if="col.inputType === 'custom' && col.component" :is="col.component" v-model="item.params![col.key].value" :index="n - 1"/>
                        </td>
                        <td class="cell">{{ item.threshold }}</td>
                        <td class="cell">{{ item.sample.entry.title }}</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
@reference '../../../../css/app.css';

.cell {
    @apply p-2;
}

.header {
    @apply p-3 text-left font-medium select-none;
}

.controls-enter-active,
.controls-leave-active {
    @apply ease-in-out duration-600 overflow-hidden;
}

.controls-enter-from,
.controls-leave-to {
    @apply opacity-0;
}

.controls-leave-to td,
.controls-leave-to td div {
    @apply ease-out duration-600 overflow-hidden max-h-0 m-0 py-0;
}
</style>
