<script setup lang="ts">
import { useProcedureStore } from '../store';
import Checkbox from 'primevue/checkbox';
import Select from 'primevue/select';
import { computed } from 'vue';

const store = useProcedureStore();
const options = computed(() => store.analyses.map((item) => ({
    id: item.id,
    label: item.sample.identifier,
})));
</script>

<template>
    <div class="flex flex-col gap-2 rounded-md border border-slate-200 bg-slate-100 p-4">
        <div class="text-lg font-semibold">Controles</div>
        <div v-for="control in store.controls" :key="control.type" class="grid grid-cols-2 gap-2">
            <div :class="[{ 'col-span-2': !control.needs_reference }, 'flex', 'items-center']">
                <Checkbox v-model="control.enabled" binary class="mr-2"/>
                {{ control.label }}
            </div>
            <div v-if="control.needs_reference">
                <Select v-model="control.reference_id" :options option-label="label" option-value="id" :disabled="!control.enabled" size="small"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
