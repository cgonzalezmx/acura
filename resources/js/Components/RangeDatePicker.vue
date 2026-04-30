<script setup lang="ts">
import DatePicker from 'primevue/datepicker';
import IftaLabel from 'primevue/iftalabel';

const today = new Date();
const start = defineModel<Date | null>('start', { required: false });
const end = defineModel<Date | null>('end', { required: true });
const dateFormat = 'dd/mm/yy';
</script>

<template>
    <div class="flex gap-2">
        <IftaLabel>
            <DatePicker v-model="start" :max-date="today" :date-format show-clear size="small" class="w-32"/>
            <label>Desde</label>
        </IftaLabel>
        <IftaLabel>
            <DatePicker v-model="end" :max-date="today" :date-format show-clear size="small" class="w-32"/>
            <label>Hasta</label>
        </IftaLabel>
    </div>
</template>
