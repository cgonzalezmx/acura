<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { computed, defineAsyncComponent, ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api'
import { Head, useForm } from '@inertiajs/vue3';
import Select from 'primevue/select';
import { format } from 'date-fns'
import Toolbar from 'primevue/toolbar';
import IftaLabel from 'primevue/iftalabel';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faWarning } from '@fortawesome/free-solid-svg-icons';
import { startOfMonth } from 'date-fns';
import RangeDatePicker from '@/Components/RangeDatePicker.vue';
import { useDialog } from 'primevue/usedialog';
import { useToast } from 'primevue/usetoast';

defineOptions({ layout: HomeLayout });

interface Props {
    analyses?: any[];
    parameters: string[];
    matrices: string[];
}

interface Analysis {
    analysis_id: number;
    parameter: string;
    isRanged: boolean;
}

type Range = 'low' | 'mid' | 'high';

type RegisterForm = {
    analyses: Analysis[];
    range: Range;
    seed?: true;
};

const today = new Date();
const possibleStates = [
    { label: 'Sin registrar', value: 0 },
    { label: 'Registrado', value: 1 },
    { label: 'Ambos', value: 2 }
];
const props = defineProps<Props>();
const analyses = computed(() => {
    return props.analyses?.map((item) => {
        return {
            ...item,
            sample: item.sample.identifier
        }
    });
});
const filters = ref();
const identifiers = computed(() => Array.from(new Set(props.analyses?.map((item) => item.sample.identifier))));
const showWarning = ref(false);
const warningMessage = ref('');
const queryForm = useForm({
    from: startOfMonth(today),
    until: today,
    state: 0,
    parameter: ''
});
const registerForm = useForm<RegisterForm>({
    analyses: [],
    range: 'mid'
}).defaults({
    analyses: [],
    range: 'mid'
});

const currentAnalysis = computed(() => registerForm.analyses[0].parameter);
const Selector = defineAsyncComponent(() => import('@/Components/DynamicDialog/Selector.vue'));
const dialog = useDialog();
const toast = useToast();

function initFilters() {
    filters.value = {
        sample: { value: null, matchMode: FilterMatchMode.EQUALS },
        matrix: { value: null, matchMode: FilterMatchMode.EQUALS }
    }
}

function submit() {
    registerForm.analyses = [];
    queryForm
        .transform((data) => {
            const dateFormat = 'yyyy-MM-dd';

            return {
                ...data,
                from: format(data.from, dateFormat),
                until: format(data.until, dateFormat),
            }
        })
        .get(route('work-orders.index'), {
            only: ['analyses'], preserveState: true,
        });
}

function checkConsitency() {
    const list = registerForm.analyses;
    if (list.length === 0) return false;
    const first = list[0];
    return list.every((item) => {
        return first.parameter === item.parameter
            && first.isRanged === item.isRanged;
    });
}

function warn(msg: string) {
    warningMessage.value = msg;
    showWarning.value = true;
}

function selectRange(confirmCallback: (range: Range) => void) {
    dialog.open(Selector, {
        props: {
            header: 'Seleccione el rango'
        },
        data: {
            options: [
                { label: 'Bajo', value: 'low' },
                { label: 'Alto', value: 'high' }
            ],
        },
        emits: {
            onConfirm(selection: Range) {
                confirmCallback(selection);
            }
        }
    });
}

function verify() {
    const empty = registerForm.analyses.length === 0;
    const consistent = checkConsitency();

    if (empty) {
        warn('No hay análisis seleccionados.');
        return false;
    }

    if (!consistent) {
        warn('No se pueden mezclar análisis. Revise su selección.');
        return false;
    }

    return true;
}

function seed() {
    const seedable: Record<string, true> = {
        'Demanada Bioquímica de Oxígeno': true,
        'Demanada Bioquímica de Oxígeno Soluble': true,
    };

    if (!seedable[currentAnalysis.value]) {
        return;
    }

    dialog.open(Selector, {})
}

function register() {
    if (!verify()) {
        return;
    }

    const notification = {
        detail: 'Lote creado',
        severity: 'success',
        life: 3000
    };

    const registerCallback = (range: Range = 'mid') => {
        registerForm.range = range;
        registerForm
            .transform((data) => {
                return {
                    ...data,
                    analyses: data.analyses.map((item) => item.analysis_id),
                    parameter: data.analyses[0].parameter
                }
            })
            .post(route('batches.store'), {
                only: ['analyses'],
                onSuccess: () => toast.add(notification)
            });
    }

    seed();

    if (registerForm.analyses[0].isRanged) {
        selectRange(registerCallback);
        return;
    }
    else {
        registerCallback('mid')
    }
}

initFilters();
</script>

<template>
    <Head title="Orden de trabajo"/>
    <h2 class="text-3xl font-semibold">Orden de trabajo</h2>
    <DataTable
        :value="analyses"
        v-model:filters="filters"
        data-key="id"
        removable-sort
        filter-display="row"
        pt:root="flex flex-col overflow-auto h-full">
        <template #header>
            <Toolbar>
                <template #start>
                    <form @submit.prevent="submit" class="flex gap-2">
                        <RangeDatePicker v-model:start="queryForm.from" v-model:end="queryForm.until"/>
                        <IftaLabel>
                            <Select v-model="queryForm.state" :options="possibleStates" option-value="value" option-label="label"/>
                            <label>Estado</label>
                        </IftaLabel>
                        <IftaLabel>
                            <Select
                                v-model="queryForm.parameter"
                                :options="parameters"
                                filter
                                auto-filter-focus
                                class="w-50"/>
                            <label>Análisis</label>
                        </IftaLabel>
                        <Button label="Filtrar" type="submit" variant="outlined" icon="fa-solid fa-magnifying-glass"/>
                    </form>
               </template>
               <template #end>
                    <Button label="Registrar" @click="register"/>
               </template>
            </Toolbar>
        </template>
        <Column>
            <template #body="{data}">
                <Checkbox
                    v-model="registerForm.analyses"
                    :value="{
                        analysis_id: data.id,
                        parameter: data.parameter,
                        isRanged: data.is_ranged
                    }"
                    :disabled="data.registered"
                />
            </template>
        </Column>
        <Column header="# Registros" field="registration_counter"/>
        <Column
            header="Muestra"
            field="sample"
            :show-filter-menu="false"
        >
            <template #filter="{ filterModel, filterCallback }">
                <Select v-model="filterModel.value" @value-change="filterCallback" :options="identifiers" show-clear/>
            </template>
            <template #body="{data}">
                {{ data.sample }}
            </template>
        </Column>
        <Column header="Matriz" field="matrix" :show-filter-menu="false">
            <template #filter="{ filterModel, filterCallback }">
                <Select v-model="filterModel.value" @value-change="filterCallback" :options="matrices" show-clear/>
            </template>
        </Column>
        <Column header="Prioridad" field="is_urgent" sortable :show-filter-match-modes="false">
            <template #body="{ data }">
                <b v-if="data.is_urgent" class="text-red-500">URGENTE</b>
                <span v-else>Normal</span>
            </template>
        </Column>
        <Column header="Análisis" field="parameter">
            <template #body="{ data }">
                {{ data.total_indexes > 1 ? `${data.parameter} ${data.index}/${data.total_indexes}` : data.parameter }}
            </template>
        </Column>
        <Column header="LMP" field="threshold"/>
        <Column header="Refrigerador" field="refrigerator"/>
    </DataTable>
    <Dialog v-model:visible="showWarning">
        <div class="flex">
            <div>
                <FontAwesomeIcon :icon="faWarning" size="3x"/>
            </div>
            <div>
                {{ warningMessage }}
            </div>
        </div>
    </Dialog>
</template>
