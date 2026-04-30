<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import DatePicker from 'primevue/datepicker';
import { Take } from '@/types/take';
import { computed } from 'vue';
import InputText from 'primevue/inputtext';

interface Props {
    totalTakes: number;
}

const props = defineProps<Props>();
const takes = defineModel<Take[]>({ required: true });
const currentDate = new Date();
const minDateForFirstTake = new Date();
minDateForFirstTake.setDate(currentDate.getDate() - 1);
minDateForFirstTake.setHours(0, 0, 0, 0);
const takeTimeLapse = props.totalTakes === 6
    ? 3
    : (props.totalTakes === 4
        ? 2
        : 1);
const maxDates = computed(() => {
    return takes.value.map((_, i) => {
        if (i === 0) {
            return new Date();
        }

        const maxDate = new Date(takes.value[i - 1].timestamp!);
        maxDate.setHours(maxDate.getHours() + takeTimeLapse + 1);
        return maxDate;
    })
});

const minDates = computed(() => {
    return takes.value.map((_, i) => {
        if (i === 0) {
            return minDateForFirstTake;
        }

        const date = new Date(takes.value[i - 1].timestamp!)
        date.setHours(date.getHours() + takeTimeLapse);

        return date;
    })
});

function handleBaseDateChange(newValue: Date | null){
    if (!newValue) return;

    const base = new Date(newValue);
    base.setSeconds(0, 0);


    takes.value[0].timestamp = base;

    for (let i = 1; i < takes.value.length; i++) {
        const d = new Date(base);
        d.setHours(base.getHours() + (i * takeTimeLapse));
        
        takes.value[i].timestamp = new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes());
    }
}
</script>

<template>
    <DataTable :value="takes">
        <Column>
            <template #body="{index}">
                {{ index + 1 }}
            </template>
        </Column>
        <Column header="Fecha y hora de muestreo">
            <template #body="{data, index}">
                <div class="flex gap-2">
                    <DatePicker
                        v-model="data.timestamp"
                        size="small"
                        date-format="dd/mm/yy"
                        show-time hour-format="24"
                        :min-date="minDates[index]!"
                        :max-date="maxDates[index]!"/>
                </div>
            </template>
        </Column>
        <Column header="Color">
            <template #body="{data}">
                <InputText v-model="data.color" size="small" class="w-36"/>
            </template>
        </Column>
        <Column header="Olor">
            <template #body="{data}">
                <InputText v-model="data.odour" size="small" class="w-36"/>
            </template>
        </Column>
        <Column header="Apariencia">
            <template #body="{data}">
                <InputText v-model="data.appearance" size="small" class="w-36"/>
            </template>
        </Column>
    </DataTable>
</template>