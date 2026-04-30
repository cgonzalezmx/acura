<script setup lang="ts">
import { onMounted, ref } from 'vue';
import Button from 'primevue/button';
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import { useEntryStore } from '../stores/useEntryStore';
import Checkbox from 'primevue/checkbox';
import { useQuoteDataStore } from '../stores/useQuoteDataStore';
import { storeToRefs } from 'pinia';
import EntryDefinition from './EntryDefinition.vue';

const entryStore = useEntryStore();
const currentEntry = ref('');
const { sampleDeliveredByClient } = storeToRefs(useQuoteDataStore());

onMounted(() => {
    let current: string;
    if (entryStore.all.length === 0) {
        const added = entryStore.add();
        current = added;
    }
    else {
        current = entryStore.all[0].entry_id;
    }

    currentEntry.value = current;
});
</script>

<template>
    <div class="mb-3">
        <Checkbox v-model="sampleDeliveredByClient" binary/>
        <span class="ml-3">Muestra traída por el cliente</span>
    </div>
    <div class="mb-3">
        <Button label="Agregar partida" icon="fa-solid fa-plus" @click="entryStore.add"/>
    </div>
    <Accordion
        :value="currentEntry"
        class="mb-3">
        <AccordionPanel
            v-for="(entry, index) in entryStore.all"
            :key="entry.entry_id"
            :value="entry.entry_id"
            class="last:border-0 border-b border-slate-400">
            <AccordionHeader
                :pt="{
                    toggleicon: ({context}) => context.active ? 'text-slate-100' : 'text-slate-600',
                    root: ({context}) => context.active
                            ? 'bg-primary-500 text-slate-100'
                            : 'bg-slate-200 text-slate-600'
                    
                }">
                <div class="flex gap-3">
                    <Button
                        v-tooltip.top="'Copiar partida'"
                        icon="fa-solid fa-copy"
                        severity="secondary"
                        @click.stop="entryStore.copy(entry)"/>
                    <Button
                        v-tooltip.top="'Eliminar partida'"
                        icon="fa-solid fa-trash-can"
                        severity="secondary"
                        @click.stop="entryStore.remove(entry.entry_id)"/>
                    <div class="flex items-center">
                        Partida {{ index + 1 }} - Punto de muestreo: {{ entry.title }}
                    </div>
                </div>
            </AccordionHeader>
            <AccordionContent :pt="{
                content: [
                    'border-x',
                    {'border-b rounded-b-md': entryStore.all.length - 1 === index},
                    'py-3'
                ]
            }"
            >
            <EntryDefinition :entry-id="entry.entry_id"/>
            </AccordionContent>
        </AccordionPanel>
    </Accordion>
</template>