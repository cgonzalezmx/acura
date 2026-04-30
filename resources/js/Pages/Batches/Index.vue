<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Toolbar from 'primevue/toolbar';
import Select from 'primevue/select';
import IftaLabel from 'primevue/iftalabel';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';

defineOptions({ layout: HomeLayout })

interface Props {
    area: {
        id: number;
        name: string;
        code: string;
    };
    parameters: string[];
    batches?: any[];
}

const possibleStatus = [
    { status: 0, label: 'En proceso' },
    { status: 1, label: 'Autorizado' },
    { status: 2, label: 'Ambos' },
];
const props = defineProps<Props>();
const form = useForm({
    selection: props.parameters[0],
    status: 0
});

function query() {
    form.get(route('batches.index', props.area.code), { only: ['batches'], preserveState: true })
}

function openSheet(id: number) {
    window.open(route('batches.show', id), '_blank');
}

function formatDate(date: Date | string | null, formatStr: string) {
    return date ? format(date, formatStr) : 'Pendiente';
}
</script>

<template>
    <div class="bg-white">
        <h2 class="text-3xl font-semibold px-4">{{ area.name }}</h2>
        <Toolbar class="m-4">
            <template #start>
                <form @submit.prevent="query" class="flex">
                    <IftaLabel class="mr-4">
                        <Select v-model="form.selection" :options="parameters" filter/>
                        <label>Análisis</label>
                    </IftaLabel>
                    <IftaLabel class="mr-4">
                        <Select v-model="form.status" :options="possibleStatus" option-label="label" option-value="status"/>
                        <label>Estatus</label>
                    </IftaLabel>
                    <Button label="Consultar" type="submit"/>
                </form>
            </template>
        </Toolbar>
        <table class="w-full text-slate-700">
            <thead>
                <tr class="border-b border-b-slate-200">
                    <th class="header">Fecha de analisis</th>
                    <th class="header">Hora de análisis</th>
                    <th class="header">Análisis</th>
                    <th class="header">No. de muestras</th>
                    <th class="header">Lote</th>
                    <th class="header">Estatus</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in batches" :key="item.id" class="border-b border-b-slate-200" @dblclick="openSheet(item.id)">
                    <td class="cell">{{ formatDate(item.analyzed_at, 'dd/MM/yyyy') }}</td>
                    <td class="cell">{{ formatDate(item.analyzed_at, 'H:m') }}</td>
                    <td class="cell">{{ item.parameter }}</td>
                    <td class="cell">{{ item.analyses_count }}</td>
                    <td class="cell">{{ item.name }}</td>
                    <td class="cell">{{ item.authorized ? 'Procesado' : 'En proceso' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
@reference '../../../css/app.css';
.cell {
    @apply p-2;
}

.header {
    @apply p-2 text-left font-medium;
}
</style>
