<script setup lang="ts">
import { computed, onMounted, provide, ref } from 'vue';
import TagTray from '@/Components/TagTray.vue';
import TreeNavigator from '@/Components/Tree/TreeNavigator.vue';
import axios from 'axios';
import { TreeNode } from 'primevue/treenode';
import RegulatoryThresholds from './RegulatoryThresholds.vue';
import { useEntryStore } from '../stores/useEntryStore';
import { useEntryHandler } from '../composables/useEntryHandler';
import { Report } from '../types';
import { useMatrixParameters } from '../composables/useMatrixParameters';
import Divider from 'primevue/divider';

interface Props {
    entryId: string;
}

const { entryId } = defineProps<Props>();
const currentReport = ref<Report & { label: string }>();
const entryStore = useEntryStore();
const entry = entryStore.get(entryId);
const entrytHandler = useEntryHandler(entry);
const reports = entrytHandler.allReports;
const matrixId = computed(() => entry.matrix_id);
//const matrixParameterStore = useMatrixParameterStore()
const { data: parameters } = useMatrixParameters(matrixId);
//const parameters = matrixParameterStore.fetchMatrix(matrixId)
const loadStructureChildren = (node: TreeNode) => axios.get(route('regulations.nodes.view.children', node.key))
const loadInstanceChildren = (node: TreeNode) => axios.get(route('regulations.instances.nodes.children', node.key));

function addReport() {
    return entrytHandler.addReport();
}

function removeReport(index: number) {
    const report = reports.value[index];
    entrytHandler.removeReport(report.report_id);
}

function onTagSelected(index: number) {
    currentReport.value = reports.value[index];
}

function displayInstanceTree(report: Report): Boolean {
    const hasBundle = report.structure.selection.value?.type === 'bundle';
    return hasBundle || Boolean(entry.is_loaded);
}

onMounted(() => {
    if (entry.is_loaded) {
        entrytHandler.setupReports();
    }
    else {
        addReport();
    }

    currentReport.value = reports.value[0];
});
</script>

<template>
    <TagTray
        :items="reports"
        tags-prefix="Reporte "
        class="my-3"
        @add-tag="addReport"
        @remove-tag="removeReport"
        @tag-selected="onTagSelected"/>
    <div class="border border-slate-200 my-3">
        <template v-for="report in reports" :key="report.report_id">
            <div class="flex">
                <template v-if="currentReport?.report_id === report.report_id">
                    <div class="w-1/2">
                        <div class="p-2 text-lg">Regulaciones</div>
                        <TreeNavigator
                            :tree-id="report.structure.id"
                            :load-children="loadStructureChildren"/>
                    </div>
                    <Divider layout="vertical"/>
                    <div v-if="displayInstanceTree(report)" class="w-1/2">
                        <div class="p-2 text-lg">Límites permisibles</div>
                        <TreeNavigator
                            :tree-id="report.instance.id"
                            :load-children="loadInstanceChildren"/>
                    </div>
                </template>
            </div>
        </template>
    </div>
    <RegulatoryThresholds :entry-id :parameters/>
    <div class="flex flex-col gap-3">
    </div>
</template>