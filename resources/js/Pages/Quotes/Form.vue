<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import Tab from 'primevue/tab';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import Client from './Partials/Client.vue';
import Button from 'primevue/button';
import { useSaveQuote } from './composables/useSaveQuote';
import { useClientDataStore } from './stores/useClientDataStore';
import { useEntryStore } from './stores/useEntryStore';
import { useQuoteDataStore } from './stores/useQuoteDataStore';
import { useContactDataStore } from './stores/useContactDataStore';
import { useSamplingSiteDataStore } from './stores/useSamplingSiteDataStore';
import { useCostStore } from './stores/useCostStore';
import { defineAsyncComponent, ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Dialog from 'primevue/dialog';

interface Props {
    quote?: any;
    selectedContact?: any;
    selectedSite?: any;
    root_nodes?: any;
}

const { quote, selectedContact, selectedSite } = defineProps<Props>()
const Sampling = defineAsyncComponent(() => import('./Partials/Sampling.vue'));
const CostBreakdown = defineAsyncComponent(() => import('./Partials/CostBreakdown.vue'));
const tabs = [
    { title: 'Datos', value: 0, component: Client },
    { title: 'Muestreo', value: 1, component: Sampling },
    { title: 'Costos', value: 2, component: CostBreakdown },
];

if (quote) {
    const clientDataStore = useClientDataStore();
    const quoteDataStore = useQuoteDataStore();
    const contactDataStore = useContactDataStore();
    const samplingSiteStore = useSamplingSiteDataStore();
    const costStore = useCostStore();
    const entryStore = useEntryStore();
    clientDataStore.sync(quote.client);
    quoteDataStore.sync(quote);
    entryStore.loadEntries(quote.entries);
    contactDataStore.client_contact_id = selectedContact?.client_contact_id;
    samplingSiteStore.client_sampling_site_id = selectedSite?.client_sampling_site_id;

    if (typeof quote.priceAdjustment !== 'undefined') {
        costStore.priceAdjustment = quote.priceAdjustment;
    }

    if (Array.isArray(quote.expenses) && quote.expenses.length > 0) {
        costStore.expenses = quote.expenses;
        costStore.globalExpensesConcept = quote.global_expenses_concept;
        costStore.globalExpensesQuantity = quote.global_expenses_quantity;
    }

    if (quote.payment_method) {
        costStore.paymentMethod = quote.payment_method;
    }
}

const page = usePage();
const toast = useToast();
const showErrors = ref(false);

watch(() => page.props.flash.message, (message) => {
    if (message) {
        toast.add({
            detail: message,
            severity: 'success',
            life: 3000
        });
    }
});

watch(() => page.props.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        showErrors.value = true;
    }
});

function save() {
    page.props.flash.message = '';
    useSaveQuote();
}
</script>

<template>
    <Dialog v-model:visible="showErrors" :draggable="false" modal header="Lista de errores">
        <div>
            <ul>
                <li v-for="(error, key) in page.props.errors" :key>
                    {{ error }}
                </li>
            </ul>
        </div>
    </Dialog>
    <Head title="Formulario Cotizaciones"/>
    <Toast/>
    <div class="h-screen p-6 flex flex-col">
        <div v-if="quote" class="flex justify-center">
            <h1 class="font-bold text-4xl">{{ quote.identifier }}</h1>
        </div>
        <div class="m-auto w-full lg:w-[90%] xl:w-[80%] h-full overflow-hidden">
            <Tabs :value="0" class="h-full overflow-hidden">
                <TabList pt:tab-list="mb-3">
                    <Tab v-for="tab in tabs" :value="tab.value">{{ tab.title }}</Tab>
                </TabList>
                <TabPanels class="flex flex-col overflow-hidden h-full">
                    <TabPanel
                        v-for="tab in tabs"
                        class="overflow-auto"
                        :key="tab.value"
                        :value="tab.value">
                        <component :is="tab.component"/>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
        <div class="flex justify-center">
            <Button
                icon="fa-solid fa-floppy-disk"
                label="Guardar"
                size="large"
                raised
                @click="save"
            />
        </div>
    </div>
</template>