<script setup lang="ts">
import Panel from 'primevue/panel';
import RadioButton from 'primevue/radiobutton';
import Tag from 'primevue/tag'
import EditableCell from '@/Components/EditableCell.vue';

interface Props {
    values: Record<string, string>[];
    rows: [string, string][];
    header: string;
    isMain: {
        option: string;
        label: string;
    };
    optionId: string;
    disabled?: boolean;
}

const props = defineProps<Props>();
const model = defineModel<any>({ required: true });
</script>

<template>
    <Panel v-for="v in values" :key="v.id ?? 0" toggleable collapsed>
        <template #header>
            <div class="flex items-center">
                <RadioButton v-model="model[optionId]" :value="v.id" :disabled/>
                <span class="ml-4 font-semibold">{{ v[header] }}</span>
                <Tag
                    v-if="v[isMain.option]"
                    :value="isMain.label"
                    rounded
                    class="ml-3"/>
            </div>
        </template>
        <table class="w-full">
            <tbody>
                <tr v-for="[label, key] in rows" :key class="border-b border-slate-200 last:border-none">
                    <td class="border-r border-slate-200 p-1">{{ label }}</td>
                    <td class="p-1" ref="inputs"><EditableCell v-model="v[key]" /></td>
                </tr>
            </tbody>
        </table>
    </Panel>
</template>

<style scoped>

</style>
