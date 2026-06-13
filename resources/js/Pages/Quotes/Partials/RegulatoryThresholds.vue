<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import { computed, ref, unref } from 'vue';
import { Parameter, Report, Threshold } from '../types';
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import InputText from 'primevue/inputtext';
import { currency } from '@/utils/formatters';
import InputNumber from 'primevue/inputnumber';
import { usePage } from '@inertiajs/vue3';
import { PageProps } from '@/types';
import { useEntryStore } from '../stores/useEntryStore';
import { pipe, groupBy, when } from 'rambda';
import Textarea from 'primevue/textarea';
import { useDebounceFn } from '@vueuse/core';
import Checkbox from 'primevue/checkbox';

interface Props {
    entryId: string;
    parameters?: Parameter[];
}

type LocalPageProps = PageProps<{ areas: [id: number, name: string][] }>;

const { entryId, parameters } = defineProps<Props>();
const analysisAreas = new Map(usePage<LocalPageProps>().props.areas);
const entryStore = useEntryStore();
const entry = entryStore.get(entryId);
const showOnlyIncluded = ref(false);
const showIncludedTooltipMessage = 'Muestra solo los parámetros cargados de un paquete y/o definidos por el usuario.';
const parameterGroups = computed(() => {
    const grouped = pipe(
        parameters ?? [],
        when(() => showOnlyIncluded.value, (params) => {
            const included = includedParameters?.value;
            return params.filter((param) => included?.[param.parameter_id]);
        }),
        groupBy((param) => String(param.area_id))
    );
    return grouped;
});
const reports = computed(() => Object.values(entry.reports).map((report, index) => {
    return {
        ...report,
        label: String.fromCharCode(index + 65)
    };
}));
const includedParameters = computed(() => entry.included_parameters);

function getThresholdValues(report: Report, parameterId: number, boundary: 'min' | 'max') {
    const customThresholds: Threshold | undefined = report.thresholds.custom.get(parameterId);
    const systemThresholds: Threshold | undefined = report.thresholds.system.get(parameterId);
    const customBoundary = customThresholds?.[boundary];
    const value = typeof customBoundary !== 'undefined'
        ? customBoundary
        : systemThresholds?.[boundary];

    return value ?? null;
}

const setCustomThresholdValues = useDebounceFn((report: Report, parameterId: number, boundary: 'min' | 'max', value: string, id: number | undefined = undefined) => {
    const customThresholds = report.thresholds.custom;

    customThresholds.set(parameterId, {
        id: entry.is_loaded ? (id ?? null) : undefined,
        parameter_id: parameterId,
        min: boundary === 'min' ? value : null,
        max: boundary === 'max' ? value : null,
        custom_boundary: boundary
    })
}, 300);

function overrideQuantity(parameterId: number, quantity: number) {
    const included = unref(includedParameters);
    const param = included?.[parameterId];
    const overrides = entry.quantityOverrides;

    if (!param && !quantity) return;

    if (param && !param.from_system && !quantity) {
        delete overrides?.[parameterId]
        delete included?.[parameterId];
        return;
    }

    overrides[parameterId] = quantity;
}

function subTotal(parameters: Parameter[]) {
    if (includedParameters) {
        const included = unref(includedParameters);
        const result = parameters.reduce((total, param) => {

            if (!included[param.parameter_id]) return { count: total.count, sum: total.sum};

            const includedParamInfo = included[param.parameter_id];
            return {
                count: total.count + includedParamInfo.quantity,
                sum: total.sum + param.price * (includedParamInfo.quantity)
            }
        }, { count: 0, sum: 0});

        return {
            count: result.count,
            sum: currency(result.sum)
        };
    }

    return {};
}

const disableShowOnlyIncluded = computed(() => {
    const included = Object.keys(includedParameters?.value ?? {});

    return !(included.length > 0);
});
</script>

<template>
    <div class="py-3">
        <span v-tooltip.top="showIncludedTooltipMessage">
            <Checkbox
                v-model="showOnlyIncluded"
                binary
                :input-id="`${entryId}-show-included`"
                :disabled="disableShowOnlyIncluded"/>
            <label :for="`${entryId}-show-included`" class="pl-3">Mostrar solo incluidos</label>
        </span>
    </div>
    <Accordion
        :value="[]" class="mb-2">
        <AccordionPanel
            v-for="(params, area) in parameterGroups"
            multiple
            lazy
            :key="area"
            :value="area" class="border-b-0">
            <AccordionHeader
                class="border border-primary-300">{{ analysisAreas.get(Number(area)) }}</AccordionHeader>
            <AccordionContent pt:content="border-x">
                <DataTable
                    v-if="params"
                    scrollable
                    scroll-height="700px"
                    :virtual-scroller-options="{ itemSize: 60 }"
                    data-key="id"
                    :value="params"
                    >
                    <ColumnGroup type="header">
                        <Row>
                            <Column header="Parámetro" :rowspan="3" class="min-w-72"/>
                            <Column header="Cantidad" :rowspan="3" class="min-w-8"/>
                            <Column header="Precio" :rowspan="3" class="min-w-28"/>
                            <Column header="Límites" :colspan="reports.length * 2"/>
                            <Column header="Metodología" :rowspan="3" class="min-w-72"/>
                        </Row>
                        <Row>
                            <Column v-for="report in reports" :key="report.report_id" :header="`Reporte ${report.label}`" :colspan="2"/>
                        </Row>
                        <Row>
                            <template v-for="report in reports" :key="report.report_id">
                                <Column header="Min"/>
                                <Column header="Max"/>
                            </template>
                        </Row>
                    </ColumnGroup>
                    <Column field="name" class="h-[60px] relative expandable">
                        <template #body="{data}">
                            <div :class="{ 'truncated hover:shadow-md hover:rounded': data.name.length > 30 ? true : false }">
                                {{ data.name }}
                            </div>
                        </template>
                    </Column>
                    <Column class="h-[60px]">
                        <template #body="{data}">
                            <InputNumber
                                fluid
                                locale="es-MX"
                                size="small"
                                :min="0"
                                :model-value="includedParameters?.[data.parameter_id]?.quantity"
                                @update:model-value="overrideQuantity(data.parameter_id, $event)"
                                :pt="{
                                    pcInputText: {
                                        root: {
                                            class: {
                                                'bg-emerald-300': includedParameters?.[data.parameter_id]?.from_main_report
                                                    && includedParameters?.[data.parameter_id]?.quantity > 0
                                                    && includedParameters?.[data.parameter_id]?.quantity === includedParameters?.[data.parameter_id]?.expected_quantity,
                                                'bg-sky-300': includedParameters?.[data.parameter_id]?.from_system
                                                    && !includedParameters?.[data.parameter_id]?.from_main_report
                                                    && includedParameters?.[data.parameter_id]?.quantity > 0,
                                                'bg-amber-300': includedParameters?.[data.parameter_id] && (!includedParameters?.[data.parameter_id]?.from_system
                                                    || includedParameters?.[data.parameter_id]?.expected_quantity !== includedParameters?.[data.parameter_id]?.quantity)
                                            }
                                        }
                                    }
                                }"/>
                        </template>
                    </Column>
                    <Column field="price" class="h-[60px]">
                        <template #body="{data}">
                            {{ currency(data.price) }}
                        </template>
                    </Column>
                    <template v-for="report in reports" :key="report.report_id">
                        <Column v-for="boundary in ['min', 'max']" :key="boundary" class="h-[60px] min-w-32">
                            <template #body="{data}">
                                <InputText
                                    :model-value="getThresholdValues(report, data.parameter_id, boundary as 'min' | 'max')"
                                    size="small"
                                    fluid
                                    @update:model-value="setCustomThresholdValues(report, data.parameter_id, boundary as 'min' | 'max', $event ?? '', data.id)"
                                    :pt="{
                                        root() {
                                            return {
                                                class: {
                                                    'bg-rose-300': report.thresholds.custom.get(data.parameter_id)?.[boundary as 'min' | 'max']
                                                }
                                            }
                                        },
                                    }"/>
                            </template>
                        </Column>
                    </template>
                    <Column field="methodology" class="h-[60px]"/>
                    <ColumnGroup type="footer">
                        <Row>
                            <Column footer="Subtotal"/>
                            <Column v-for="data in subTotal(params)">
                                <template #footer>
                                    {{ data }}
                                </template>
                            </Column>
                            <Column :colspan="1 + reports.length * 2"/>
                        </Row>
                    </ColumnGroup>
                </DataTable>
            </AccordionContent>
        </AccordionPanel>
        <AccordionPanel v-if="parameters?.length" :value="99" class="border-b-0">
            <AccordionHeader class="border border-primary-300">Observaciones sobre los límites máximos permisibles</AccordionHeader>
            <AccordionContent pt:content="border-x border-b">
                <div v-for="report in reports" :key="report.report_id" class="flex flex-col">
                    <span class="text-lg font-medium">Reporte {{ report.label }}</span>
                    <Textarea
                        v-model="report.observation.value"
                        rows="5"
                        class="resize-none w-full"/>
                </div>
            </AccordionContent>
        </AccordionPanel>
    </Accordion>
</template>

<style scoped>
.truncated {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    line-clamp: 1;
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
    overflow: hidden;
}

.expandable:hover .truncated {
    position: absolute;
    background: #fff;
    top: 0;
    left: 0;
    z-index: 10;
    padding: 16px 12px;
    white-space: normal;
    max-width: 300px;
    width: max-content;
    display: block;
    -webkit-line-clamp: unset;
    line-clamp: unset;
    overflow: visible;
    margin: 4px;
}
</style>
