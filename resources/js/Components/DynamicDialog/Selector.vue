<script setup lang="ts">
import { useDialogRef } from '@/composables/useDialogRef';
import Button from 'primevue/button';
import { defineAsyncComponent, ref, useId } from 'vue';

const emit = defineEmits<{
    confirm: [value: any];
}>();

interface Data {
    options: Array<{ label: string; value: any; }>;
    modeSelection?: 'single' | 'multi';
}
const dialoRef = useDialogRef<Data>();
const Checkbox = defineAsyncComponent(() => import('primevue/checkbox'));
const RadioButton = defineAsyncComponent(() => import('primevue/radiobutton'));
const options = dialoRef?.value?.data?.options?.map((item => ({ id: useId(), ...item })));
const selection = ref<any | any[] | null>(null);

function confirm() {
    emit('confirm', selection.value);
    dialoRef?.value.close();
}
</script>

<template>
    <div class="grid grid-cols-2 gap-x-3 gap-y-1 place-items-center">
        <template v-for="item in options">
            <label :for="item.id">{{ item.label }}</label>
            <component
                :is="dialoRef?.data.modeSelection === 'multi' ? Checkbox : RadioButton"
                v-model="selection"
                :value="item.value"
                :inputId="item.id"/>
        </template>
    </div>
    <div class="flex justify-between">
        <Button label="Confirmar" @click="confirm"/>
    </div>
</template>