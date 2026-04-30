<script setup lang="ts">
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import { useProcedureStore } from '../store';

const store = useProcedureStore();
</script>

<template>
    <div class="md:grid md:grid-cols-2 lg:grid-cols-4 p-4">
        <IftaLabel v-for="(item, key) in store.config?.extendedAttributes">
            <InputNumber v-model="store.params![key]" :max-fraction-digits="6" size="small"/>
            <label>{{ item.header }}</label>
        </IftaLabel>
    </div>
</template>

<style scoped>

</style>
