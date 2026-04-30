<script setup lang="ts">
import RadioButton from 'primevue/radiobutton';
import InputNumber from 'primevue/inputnumber';
import { computed } from 'vue';

interface Props {
    index: number;
}

interface Model {
    index: number;
    documental: number;
}

const props = defineProps<Props>();
const model = defineModel<Model>({ required: true, });
const selected = computed(() => props.index === model.value.index);
const referencial = computed(() => {
    return props.index === model.value.index
        ? model.value.documental
        : null;
});
const updateReferencial = (value: number) => model.value.documental = value;
</script>

<template>
    <div class="flex items-center gap-2">
        <RadioButton v-model="model.index" :value="index"/>
        <InputNumber :modelValue="referencial" @update:modelValue="updateReferencial" :disabled="!selected" size="small" fluid/>
    </div>
</template>

<style scoped>

</style>
