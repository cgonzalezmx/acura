<script setup lang="ts">
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanel from 'primevue/tabpanel';
import TabPanels from 'primevue/tabpanels';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Parameters from './Parameters.vue';
import { defineAsyncComponent } from 'vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    parameters: any[];
    labMatrices: any[];
    methodologies: any[];
    sampleContainers: any[];
    analysisAreas: any[];
    labelColors: any[];
    samplePreservers: any[];
    sampleStorages: any[];
    quoteRemarks: any[];
    samplingRemarks: any[];
    groups?: any[];
}

const props = defineProps<Props>();
defineOptions({ layout: HomeLayout });
const Groups = defineAsyncComponent(() => import('./Groups.vue'));
</script>

<template>
    <Head titel="Parámetros"/>
    <Tabs value="0" class="h-full overflow-hidden" lazy>
        <TabList pt:tab-list="mb-2">
            <Tab value="0">Parámetros</Tab>
            <Tab value="1">Grupos</Tab>
        </TabList>
        <TabPanels class="flex flex-col overflow-hidden h-full p-0">
            <TabPanel value="0" class="overflow-auto p-6 flex flex-col">
                <Parameters
                    :analysis-areas
                    :lab-matrices
                    :label-colors
                    :methodologies
                    :parameters
                    :quote-remarks
                    :sample-containers
                    :sample-preservers
                    :sample-storages
                    :sampling-remarks/>
            </TabPanel>
            <TabPanel value="1" class="overflow-auto p-6">
                <Groups
                    :items="groups"
                    :sample-containers
                    :sample-preservers
                    :label-colors
                    :parameters
                    :analysis-areas
                    :lab-matrices/>
            </TabPanel>
        </TabPanels>
    </Tabs>
</template>