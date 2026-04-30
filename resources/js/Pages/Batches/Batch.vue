<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AnalysisTable from './Partials/AnalysisTable.vue';
import Metadata from './Partials/Metadata.vue';
import Button from 'primevue/button';
import { useProcedureStore } from './store';
import ExtendedParameters from './Partials/ExtendedParameters.vue';
import { computed, defineAsyncComponent, unref } from 'vue';
import Controls from './Partials/Controls.vue';
import { AnalysisLikeControl, Batch } from './types';
import { FormDataConvertible } from '@inertiajs/core';
import MultiRowAnalysisTable from './Partials/MultiRowAnalysisTable.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

interface Props {
    batch: Batch;
    analyses: any[];
    refrigerators: any[];
}

const props = defineProps<Props>();
const store = useProcedureStore();
const print = () => window.print();
const hasControls = computed(() => store.controls ? true : false);
const Quality = defineAsyncComponent(() => import('./Partials/Quality.vue'));
const Duplicate = defineAsyncComponent(() => import('./Partials/Duplicate.vue'));
const Spiked = defineAsyncComponent(() => import('./Partials/Spiked.vue'));
const authorizable = computed(() => !props.batch.authorized);
const toast = useToast();

function filterControls(controls: Record<string, AnalysisLikeControl>) {
    if (!controls) return;
    const object: Record<string, AnalysisLikeControl> = {};

    Object.entries(controls).forEach(([key, con]) => {
        if (con.enabled) object[key] = unref(con);
    });

    return object;
}

function save() {
    router.patch(route('batches.update', props.batch.id), {
        batch: store.procedure,
        analyses: store.analyses.map((item) => {
            const params: Record<string, any> = {};

            if (store.config?.extendedColumns) {
                for (const col of store.config?.extendedColumns) {
                    if (col.inputType !== 'info') {
                        params[col.key] = unref(item.params![col.key]);
                    }
                }
            }

            return {
                id: item.id,
                result: unref(item.result),
                reported_result: unref(item.reported_result),
                params
            };
        }),
        params: store.params,
        controls: filterControls(store.controls as Record<string, AnalysisLikeControl>) as unknown as FormDataConvertible,
    });
}

function authorize() {
    router.post(route('batches.authorize', props.batch.id), {}, {
        only: ['batch'],
        onSuccess() {
            toast.add({
                detail: 'Lote autorizado',
                life: 3000,
                severity: 'success',
            });
        },
        preserveState: true,
    });
}
</script>

<template>
    <div class="w-[95%] xl:w-[70%] m-auto">
        <h1 class="text-5xl font-bold py-5">
            {{ `${batch.analysis_area.name} ${props.batch.parameter} - ${props.batch.matrix}` }}
        </h1>
        <div :class="[{'flex gap-4': hasControls}, 'w-full']">
            <Metadata :class="[{'w-2/3': hasControls}]"/>
            <Controls v-if="hasControls" class="w-1/3"/>
        </div>
        <div>
            <ExtendedParameters/>
        </div>
        <div class="flex my-4 justify-end print:hidden">
            <div class="flex gap-3">
                <template v-if="!batch.authorized">
                    <Button v-if="authorizable" label="Guardar" icon="fa-solid fa-floppy-disk" @click="save"/>
                    <Button v-if="authorizable" label="Autorizar" icon="fa-solid fa-check" severity="success" @click="authorize"/>
                </template>
                <Button label="Imprimir" icon="fa-solid fa-print" variant="outlined" @click="print"/>
            </div>
        </div>
        <MultiRowAnalysisTable v-if="store.config?.rowsPerAnalysis"/>
        <AnalysisTable v-else/>
        <br>
        <div class="flex flex-col gap-4 mb-10">
            <Quality v-if="store?.controls?.quality?.enabled"/>
            <Duplicate v-if="store?.controls?.duplicate?.enabled"/>
            <Spiked v-if="store?.controls?.spiked?.enabled"/>
        </div>
    </div>
    <Toast/>
</template>

<style scoped>
</style>
