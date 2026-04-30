<script setup lang="ts">
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import { useProcedureStore } from '../store';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSquarePlus } from '@fortawesome/free-solid-svg-icons';
import { ref } from 'vue';

const store = useProcedureStore();
const toggleFullResult = ref(false);
</script>

<template>
    <div>
        <table class="w-full ease-in-out duration-700">
            <thead>
                <tr class="text-sm">
                    <th class="header">Clave muestra</th>
                    <th v-for="col in store.config?.extendedColumns" :key="col.key" class="header">{{ col.header }}</th>
                    <th @click="toggleFullResult = !toggleFullResult" :class="{ 'cursor-pointer': store.config?.result?.isReadOnly }">
                        <div class="flex select-none">
                            <div class="select-none">{{ store.config?.result?.header }}</div>
                            <div v-if="store.config?.result?.isReadOnly" class="flex items-center pl-2 select-none">
                                <FontAwesomeIcon :icon="faSquarePlus" size="lg" :class="{ 'text-slate-300': !toggleFullResult }"/>
                            </div>
                        </div>
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
                                <div>
                                    {{ con.label }}
                                </div>
                            </td>
                            <td v-for="col in store.config?.extendedColumns" :key="col.key" class="cell">
                                <div>
                                    <InputText v-if="col.inputType === 'text'" v-model="con.params![col.key]" size="small" fluid/>
                                    <InputNumber v-if="col.inputType === 'number'" v-model="con.params![col.key]" :max-fraction-digits="5" size="small" fluid class="font-mono"/>
                                    <span v-if="col.inputType === 'info'" :class="{ 'font-mono': typeof con.params![col.key] === 'number' }">{{ con.params![col.key] }}</span>
                                </div>
                            </td>
                            <td class="cell">
                                <div>
                                    <div v-if="store.config?.result?.isReadOnly" :class="{ 'font-mono': typeof con.result === 'number' }">
                                        {{ toggleFullResult ? con.result : con.reported_result }}
                                    </div>
                                    <InputText v-else v-model="con.result"/>
                                </div>
                            </td>
                        </tr>
                    </template>
                </TransitionGroup>
                <tr v-for="item in store.analyses" :key="item.id" class="border-y border-y-slate-200">
                    <td class="cell">{{ item.sample.identifier }}</td>
                    <td v-for="col in store.config?.extendedColumns" :key="col.key" class="cell">
                        <InputText v-if="col.inputType === 'text'" v-model="item.params![col.key].value" size="small" fluid/>
                        <InputNumber v-if="col.inputType === 'number'" v-model="item.params![col.key].value" :max-fraction-digits="5" size="small" fluid/>
                        <span v-if="col.inputType === 'info'" :class="{ 'font-mono': typeof item.params![col.key].value === 'number' }">{{ item.params![col.key].value }}</span>
                    </td>
                    <td class="cell" :class="[store.config?.veredictClass?.(item)]">
                        <div v-if="store.config?.result?.isReadOnly" :class="{ 'font-mono': typeof item.result.value === 'number' }">
                            {{ toggleFullResult ? item.result : item.reported_result }}
                        </div>
                        <InputText v-else v-model="item.result"/>
                    </td>
                    <td class="cell">{{ item.threshold }}</td>
                    <td class="cell">{{ item.sample.entry.title }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
@reference '../../../../css/app.css';

.cell {
    @apply p-3;
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
