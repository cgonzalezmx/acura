<script setup lang="ts">
import InputText from 'primevue/inputtext';
import IftaLabel from 'primevue/iftalabel';
import { useDialogRef } from '@/composables/useDialogRef';
import { Client } from '@/types/client';
import { useForm } from '@inertiajs/vue3';
import Fieldset from 'primevue/fieldset';
import Panel from 'primevue/panel';

interface Props {
    client: Client;
}

const dialogRef = useDialogRef<Props>();
const { client } = dialogRef?.value.data;

const form = useForm({
    name: client.name ?? '',
    company: client.company ?? '',
    industry_sector: client.industry_sector ?? '',
    website: client.website ?? '',
    street: client.street ?? '',
    external_number: client.external_number ?? '',
    internal_number: client.internal_number ?? '',
    neighborhood: client.neighborhood ?? '',
    zip_code: client.zip_code ?? '',
    city: client.city ?? '',
    state: client.state ?? ''
});
</script>

<template>
    <form>

    </form>
</template>

<style scoped>
</style>
