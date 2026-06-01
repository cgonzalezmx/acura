<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import ConfirmPopup from 'primevue/confirmpopup';
import { DynamicDialogInstance } from 'primevue/dynamicdialogoptions';
import { inject, Ref } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

interface DialogRef extends DynamicDialogInstance {
    data: {
        entries: any[];
    }
}

const dialogRef = inject<Ref<DialogRef>>('dialogRef');
const toast = useToast();
const confirm = useConfirm();

function authorize(event: Event, entry: any) {
    confirm.require({
        target: event.currentTarget as HTMLElement,
        message: `¿Autorizar ${entry.title}?`,
        acceptProps: {
            label: 'Confirmar'
        },
        accept() {
            axios.post(route('sampling-formats.store'), {
                entry_id: entry.id
            }).then(() => {
                toast.add({
                    detail: `Se autorizo ${entry.title}`,
                    severity: 'success',
                    life: 3000
                });
                dialogRef?.value.close();
            })
        },
    });
}
</script>

<template>
    <ConfirmPopup/>
    <DataTable :value="dialogRef?.data.entries">
        <Column header="Partida">
            <template #body="{ index }">
                {{ index + 1 }}
            </template>
        </Column>
        <Column header="Punto de muestreo" field="title"/>
        <Column header="Muestreo">
            <template #body="{ data }">
                <Button
                    :label="data.authorized ? 'Autorizada' : 'Autorizar'"
                    :icon="data.authorized ? 'fa-solid fa-check' : ''"
                    :severity="data.authorized ? 'success' : ''"
                    @click="authorize($event, data)"
                    :disabled="data.authorized"/>
            </template>
        </Column>
    </DataTable>
</template>
