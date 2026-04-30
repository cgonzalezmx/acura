<script setup lang="ts">
import { computed, ref } from 'vue';
import InputText from 'primevue/inputtext';

interface Props {
    min?: Date;
    max?: Date;
}

const props = defineProps<Props>();
const model = defineModel<Date | null>({ required: true });
const date = ref('');
function formtteDate(date: Date) {
    const y = date.getFullYear();
    const m = (date.getMonth() + 1).toString().padStart(2, '0');
    const d = date.getDate().toString().padStart(2, '0');
    
    return `${y}-${m}-${d}`;
}
const minFormatted = computed(() => {
    if (!props.min) return '';
    
    return formtteDate(props.min);
});

const maxFormatted = computed(() => {
    if (!props.max) return '';
    
    return formtteDate(props.max);
});

function update(str: string | undefined) {
    if (!str) return;
    const [year, month, day] = str.split('-');
    const d = new Date();
    d.setFullYear(Number(year));
    d.setMonth(Number(month) - 1);
    d.setDate(Number(day));
    d.setSeconds(0, 0);
    date.value = str;
    model.value = d;
}
</script>

<template>
    <InputText type="date" :model-value="date" @update:model-value="update"/>
</template>