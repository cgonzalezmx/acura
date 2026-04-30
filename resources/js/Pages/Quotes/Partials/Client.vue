<script setup lang="ts">
import InputText from 'primevue/inputtext';
import IftaLabel from 'primevue/iftalabel';
import AutoComplete, { AutoCompleteCompleteEvent, AutoCompleteOptionSelectEvent } from 'primevue/autocomplete';
import { computed, ComputedRef, defineAsyncComponent, h, onMounted, Ref, ref } from 'vue';
import axios from 'axios';
import { ClientContact, ClientSamplingSite } from '@/types/client';
import Panel from 'primevue/panel';
import { useClientDataStore } from '../stores/useClientDataStore';
import { storeToRefs } from 'pinia';
import Checkbox from 'primevue/checkbox';
import { useQuoteDataStore } from '../stores/useQuoteDataStore';
import Textarea from 'primevue/textarea';
import { useContactDataStore } from '../stores/useContactDataStore';
import { useSamplingSiteDataStore } from '../stores/useSamplingSiteDataStore';
import { usePage } from '@inertiajs/vue3';
import ClientAsset from './ClientAsset.vue';

const pageProps = usePage().props;
const Message = defineAsyncComponent(() => import('primevue/message'));
const page = usePage();
const errors = computed(() => pageProps.errors);
const quoteDataStore = useQuoteDataStore();
const clientDataStore = useClientDataStore();
const contactDataStore = useContactDataStore();
const samplingSiteStore = useSamplingSiteDataStore();
const clientDataRefs = storeToRefs(clientDataStore);
const clientSuggestions = ref();
const contacts = ref<ClientContact[]>([]);
const sites = ref<ClientSamplingSite[]>([]);
const clientDataFields: [string, Ref<string, string>, ComputedRef<any>?][] = [
    ['Tipo de industria', clientDataRefs.industry_sector, ],
    ['Calle y número', clientDataRefs.address, computed(() => page.props.errors['client.address'])],
    ['Colonia', clientDataRefs.neighborhood, computed(() => page.props.errors['client.neighborhood'])],
    ['Código postal', clientDataRefs.zip_code, computed(() => page.props.errors['client.zip_code'])],
    ['Ciudad', clientDataRefs.city, computed(() => page.props.errors['client.city'])],
    ['Estado', clientDataRefs.state, computed(() => page.props.errors['client.state'])]
];

const search = (event: AutoCompleteCompleteEvent) => {
    axios.get(route('clients.search'), {
        params: {
            term: event.query
        }
    }).then((response) => clientSuggestions.value = response.data);
}

const focus = (event: Event) => {
    (event.target as HTMLInputElement).select();
}

const requiredClientFields = computed(() => {
    return clientDataFields.some(([,,error]) => Boolean(error?.value));
});

async function fetchClientData(event: AutoCompleteOptionSelectEvent) {
    const { data } = await axios.get(route('clients.show', event.value.id),{
        params: { context: 'quote_edition' }
    });

    const contactList = data.contacts as ClientContact[];
    const siteList = data.sampling_sites as ClientSamplingSite[];

    clientDataStore.sync(data);
    contacts.value = contactList;
    sites.value = siteList;
    contactDataStore.client_contact_id = contactList.find((c) => c.is_main_contact)?.id ?? 0;
    samplingSiteStore.client_sampling_site_id = siteList.find((s) => s.is_main_site)?.id ?? 0;
}

function onUpdateModelValue(value: any) {
    if (typeof value === 'string') {
        clientDataStore.name = value;
        return;
    }

    clientDataStore.name = value.name;
}

onMounted(() => {
    if (pageProps.quote) {
        contacts.value = pageProps.contacts as ClientContact[];
        sites.value = pageProps.sites as ClientSamplingSite[];
    }
})
</script>

<template>
    <div class="flex flex-col xl:flex-row xl:flex-wrap gap-3">
        <Panel header="Cliente" class="xl:flex-1" pt:content:class="grid gap-4">
            <IftaLabel>
                <AutoComplete
                    :model-value="clientDataStore.name"
                    option-label="name"
                    size="small"
                    pt:root="w-full"
                    :suggestions="clientSuggestions"
                    :delay="1000"
                    :invalid="Boolean(page.props.errors['client.name'])"
                    :pt="{
                        pcInputText: {
                            root: 'w-full'
                        }
                    }"
                    @update:model-value="onUpdateModelValue"
                    @complete="search"
                    @option-select="fetchClientData"
                    @focus="focus">
                </AutoComplete>
                <label>Nombre</label>
            </IftaLabel>
            <IftaLabel v-for="([label, field, error], index) in clientDataFields" :key="`${field}-${index}`">
                <InputText v-model="field.value" size="small" class="w-full" :invalid="Boolean(error?.value)"/>
                <label>{{ label }}</label>
            </IftaLabel>

            <Message v-if="requiredClientFields" variant="simple" severity="error">* Campos requeridos</Message>
        </Panel>
        <Panel
            header="Contacto del cliente"
            class="xl:flex-1 flex flex-col"
            pt:content-container="h-full flex flex-col"
            pt:content="flex-1 flex flex-col gap-2">
            <ClientAsset v-model="contactDataStore"
                :values="contacts"
                :rows="[
                    ['Cliente', 'name'],
                    ['Teléfono', 'phone'],
                    ['Teléfono celular', 'cellphone'],
                    ['Email', 'email']
                ]"
                header="name"
                :is-main="{
                    option: 'is_main_contact',
                    label: 'Principal'
                }"
                option-id="client_contact_id"
            />
        </Panel>
        <Panel
            header="Lugar de muestreo"
            class="xl:flex-1"
            pt:content="flex flex-col gap-2">
            <div class="mb-3">
                <Checkbox v-model="quoteDataStore.clientDataAsSamplingSite" binary />
                <span class="ml-3">Mismos datos del cliente</span>
            </div>
            <ClientAsset
                v-model="samplingSiteStore"
                :values="sites"
                :rows="[
                    ['Empresa', 'name'],
                    ['Tipo de industria', 'industry_sector'],
                    ['Dirección', 'address'],
                    ['Colonia', 'neighborhood'],
                    ['Ciudad', 'city'],
                    ['C.P.', 'zip_code'],
                    ['Estado', 'state'],
                    ['Contacto', 'contact_name'],
                    ['Teléfono', 'contact_phone']
                ]"
                header="name"
                :is-main="{
                    option: 'is_main_site',
                    label: 'Principal'
                }"
                option-id="client_sampling_site_id"
                :disabled="quoteDataStore.clientDataAsSamplingSite"
            />
        </Panel>
    </div>
    <div class="flex flex-col xl:flex-row gap-3 mt-3">
        <Panel class="xl:flex-1">
            <IftaLabel class="mb-4">
                <InputText v-model="quoteDataStore.validity" fluid/>
                <label>Vigencia</label>
            </IftaLabel>
            <IftaLabel>
                <Textarea
                    v-model="quoteDataStore.objective"
                    rows="3"
                    :invalid="Boolean(errors.quote_objective)"
                    class="resize-none"
                    fluid
                    />
                <label>Objetivo de la prueba</label>
            </IftaLabel>
        </Panel>
        <Panel header="Notas adicionales" class="xl:flex-1">
            <Textarea v-model="quoteDataStore.notes" rows="5" fluid class="resize-none"></Textarea>
        </Panel>
    </div>
</template>
