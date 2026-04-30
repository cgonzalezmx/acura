<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import InfoTag from './Partials/InfoTag';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Card from 'primevue/card';
import { resource } from '@/utils/resources';
import { nanoid } from 'nanoid';
import Dialog from 'primevue/dialog';
import { Field, Warning } from './Partials/types';

interface CatalogItem {
    title: string;
    resource: any;
    data: any,
    fields: Field[];
    warning: Warning;
}

defineOptions({ layout: HomeLayout })

const props = defineProps([
    'analysisAreas',
    'labMatrices',
    'parameterCategories',
    'sampleContainers',
    'labelColors',
    'samplePreservers',
    'sampleStorages',
    'methodologies',
    'measurementUnits',
    'samplingRemarks',
    'quoteRemarks'
]);

const field = (label: string, item: string, type: 'string' | 'text' = 'string') => ({
    label,
    item,
    labelIdentifier: nanoid(10),
    type
});

const lists: CatalogItem[] = [
    {
        title: 'Áreas de Análisis',
        resource: { route: 'analysis-areas', only: ['analysisAreas']},
        data: computed(() => props.analysisAreas),
        fields: [
            field('Nombre', 'name'),
            field('Código', 'code')
        ],
        warning: {
            field: 'name',
            label: 'Área de análisis:'
        }
    },
    {
        title: 'Matrices de Laboratorio',
        resource: resource('lab-matrices', { only: ['labMatrices'] }),
        data: computed(() => props.labMatrices),
        fields: [
            field('Nombre', 'name'),
            field('Código', 'code')
        ],
        warning: {
            field: 'name',
            label: 'Matriz:'
        }
    },
    {
        title: 'Categorías de parámetros',
        resource: resource('parameter-categories', { only: ['parameterCategories'] }),
        data: computed(() => props.parameterCategories),
        fields: [
            field('Nombre', 'name'),
            field('Descripción', 'description', 'text')
        ],
        warning: {
            field: 'name',
            label: 'Categoría de parámetros:'
        }
    },
    {
        title: 'Lista de contenedores para muestras',
        resource: resource('sample-containers', { only: ['sampleContainers'] }),
        data: computed(() => props.sampleContainers),
        fields: [
            field('Nombre', 'name'),
            field('Descripción', 'description', 'text')
        ],
        warning: {
            field: 'name',
            label: 'Contenedor:'
        }
    },
    {
        title: 'Colores de etiqueta para recipientes',
        resource: resource('label-colors', { only: ['labelColors'] }),
        data: computed(() => props.labelColors),
        fields: [field('Color', 'color')],
        warning: {
            field: 'color',
            label: 'Color de etiqueta:'
        }
    },
    {
        title: 'Preservadores de muestras',
        resource: resource('sample-preservers', { only: ['samplePreservers'] }),
        data: computed(() => props.samplePreservers),
        fields: [
            field('Nombre', 'name'),
            field('Descripción', 'description', 'text')
        ],
        warning: {
            field: 'name',
            label: 'Preservador:'
        }
    },
    {
        title: 'Almacenadores',
        resource: resource('sample-storages', { only: ['sampleStorages'] }),
        data: computed(() => props.sampleStorages),
        fields: [
            field('Identificador', 'identifier'),
            field('Descripción', 'description', 'text')
        ],
        warning: {
            field: 'identifier',
            label: 'Almacenador:'
        }
    },
    {
        title: 'Metodologías',
        resource: resource('methodologies', { only: ['methodologies'] }),
        data: computed(() => props.methodologies),
        fields: [field('Método', 'name')],
        warning: {
            field: 'name',
            label: 'Método:'
        }
    },
    {
        title: 'Unidades de Medición',
        resource: resource('measurement-units', { only: ['measurementUnits'] }),
        data: computed(() => props.measurementUnits),
        fields: [field('Unidad', 'unit')],
        warning: {
            field: 'unit',
            label: 'Unidad de medición:'
        }
    },
    {
        title: 'Observaciones de Plan de muestreo',
        resource: resource('sampling-remarks', { only: ['samplingRemarks'] }),
        data: computed(() => props.samplingRemarks),
        fields: [
            field('Código', 'code'),
            field('Descripción', 'description', 'text')
        ],
        warning: {
            field: 'code',
            label: 'Observación de plan de muestreo:'
        }
    },
    {
        title: 'Observaciones de cotización',
        resource: resource('quote-remarks', { only: ['quoteRemarks'] }),
        data: computed(() => props.quoteRemarks),
        fields: [
            field('Código', 'code'),
            field('Descripción', 'description', 'text')
        ],
        warning: {
            field: 'code',
            label: 'Observación de cotización:'
        }
    }
];
const showDialog = ref(false);
const registry = ref();
const editingState = reactive({
    status: 'update' as 'update' | 'delete',
    index: 0,
    item: {}
});

</script>

<template>
    <Head title="Catálogo"/>
    <div class="flex flex-col items-center gap-5 mt-5 mb-5">
        <Card
            v-for="list in lists"
            :key="list.data.id"
            class="w-4/5 h-[36rem] flex"
            pt:content:class="overflow-hidden"
            pt:body:class="overflow-hidden">
            <template #title>
                <h2 class="text-xl font-bold">{{ list.title }}</h2>
            </template>
            <template #content>
                <DataTable
                    :value="list.data.value"
                    scrollable
                    scroll-height="flex">
                    <Column header="Datos" class="w-[50%]">
                        <template #body="{ data }">
                            <template v-for="f in list.fields">
                                <span class="text-xs text-surface-500">{{ f.label }}</span>
                                <div>{{ data[f.item] }}</div>
                            </template>
                        </template>
                    </Column>
                    <Column header="Registro" class="w-[40%]">
                        <template #body="{ data }">
                            <InfoTag
                                title="Creación"
                                :user="data.created_by?.name"
                                :date="data.created_at"/>
                            <InfoTag
                                v-if="data.updated_by"
                                title="Actualización"
                                :user="data.updated_by?.name"
                                :date="data.updated_at"/>
                        </template>
                    </Column>
                    <Column>
                        <template #body="{ data }">
                            <Button v-tooltip.top="'Editar'" icon="fa-solid fa-pen"/>
                        </template>
                    </Column>
                    <Column>
                        <template #body>
                            <Button
                                v-tooltip.top="'Eliminar'"
                                icon="fa-regular fa-trash-can"
                                severity="danger"
                                outlined/>
                        </template>
                    </Column>
                </DataTable>
            </template>
            <template #footer>
                <Button
                    label="Agregar"
                    icon="fa-solid fa-plus"
                    raised
                    class="sticky bottom-0"/>
            </template>
        </Card>
    </div>
</template>