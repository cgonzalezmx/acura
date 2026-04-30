<script setup lang="ts">
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanel from 'primevue/tabpanel';
import TabPanels from 'primevue/tabpanels';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Users from './Users.vue';
import { defineAsyncComponent, provide } from 'vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

interface Props {
    activeUsers: any;
    activeRoles?: any;
}

const props = defineProps<Props>();
const Roles = defineAsyncComponent(() => import('./Roles.vue'));

provide('activeRoles', props.activeRoles);
</script>

<template>
    <Head title="Usuarios"/>
    <Tabs value="0" lazy>
        <TabList>
            <Tab value="0">Usuarios</Tab>
            <Tab value="1">Roles</Tab>
        </TabList>
        <TabPanels>
            <TabPanel value="0">
                <Users :active-users/>
            </TabPanel>
            <TabPanel value="1">
                <Roles :active-roles/>
            </TabPanel>
        </TabPanels>
    </Tabs>
</template>