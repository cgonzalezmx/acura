<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import IftaLabel from 'primevue/iftalabel';
import Textarea from 'primevue/textarea';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import InputText from 'primevue/inputtext';
import EditableList from '@/Components/EditableList.vue';
import InfoTag from './InfoTag';
import { resource } from '@/utils/resources';
import { nanoid } from 'nanoid';
import { Field, Warning } from './Partials/types';
import Card from 'primevue/card';

interface CatalogItem {
    title: string;
    resource: any;
    data: any,
    fields: Field[];
    warning: Warning
}

defineOptions({ layout: HomeLayout})

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
])

const field = (label: string, item: string, type: 'string' | 'text' = 'string') => ({
    label,
    item,
    labelIdentifier: nanoid(10),
    type
});

const lists: CatalogItem[] = [
    {
        title: 'Áreas de Análisis',
        resource: resource('areas', { only: ['analysisAreas'] }),
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
        resource: resource('matrices', { only: ['labMatrices'] }),
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
        resource: resource('containers', { only: ['sampleContainers'] }),
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
        resource: resource('preservers', { only: ['samplePreservers'] }),
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
        resource: resource('storages', { only: ['sampleStorages'] }),
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
</script>

<template>
    <Head title="Catálogo"/>
    <div class="flex flex-col m-6">
        <h1>Catálogo</h1>
        <div class="flex flex-col items-center">
            <div class="flex flex-col gap-3 xl:w-3/4">
                <Card
                    v-for="({title, resource, fields, warning, data}) in lists"
                    :pt="{
                        content: { class: 'h-[36rem]'}
                    }">
                    <template #content>
                        <EditableList
                            :title
                            :items="data.value"
                            @store="resource.store"
                            @update="resource.update"
                            @delete="resource.destroy">
                            <template #display="{ item }">
                                <div class="flex flex-col gap-1">
                                    <template v-for="f in fields">
                                        <span class="text-xs text-surface-500">{{ f.label }}</span>
                                        <div>{{ item[f.item] }}</div>
                                    </template>
                                </div>
                            </template>
                            <template #edition="{ currentItem }">
                                <div class="flex flex-col gap-3">
                                    <IftaLabel
                                        v-for="f in fields">
                                        <label :for="f.labelIdentifier">{{ f.label }}</label>
                                        <InputText
                                            v-if="f.type === 'string'"
                                            v-model="currentItem[f.item]"
                                            :id="f.labelIdentifier"
                                            class="w-full"/>
                                        <Textarea
                                            v-if="f.type === 'text'"
                                            v-model="currentItem[f.item]"
                                            rows="2"
                                            :id="f.labelIdentifier"
                                            class="w-full resize-none"/>
                                    </IftaLabel>
                                </div>
                            </template>
                            <template #summary="{ item }">
                                <InfoTag
                                    title="Creación"
                                    :user="item.created_by?.name"
                                    :date="item.created_at"/>
                                <InfoTag
                                    v-if="item.updated_by"
                                    title="Actualización"
                                    :user="item.updated_by?.name"
                                    :date="item.updated_at"/>
                            </template>
                            <template #deletion-warning="{ currentItem }">
                                <span v-if="warning.label" v-html="warning.label + '&nbsp;'"/>
                                <b>{{ currentItem[warning.field] }}</b>
                            </template>
                        </EditableList>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>