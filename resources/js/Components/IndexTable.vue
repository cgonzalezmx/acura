<script setup lang="ts">
import { computed, ref } from 'vue';
import DataTable from 'primevue/datatable';
import Toolbar from 'primevue/toolbar';
import RangeDatePicker from './RangeDatePicker.vue';
import { FilterMatchMode } from '@primevue/core';
import { startOfMonth, format } from 'date-fns';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { ConfirmationDialogConfig } from '@/types/confirmationDialog';
import { useDialog } from 'primevue/usedialog';
import ConfirmationDialog from './ConfirmationDialog.vue';
import { useToast } from 'primevue/usetoast';
import { router } from '@inertiajs/vue3';
import { MenuItem } from 'primevue/menuitem';
import Menubar from 'primevue/menubar';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

interface Props {
    value: any[];
    menuItems: MenuItem[];
    globalFilters?: string[];
}

const props = defineProps<Props>();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const selection = ref();
const date = new Date();
const firstOfMonth = startOfMonth(date);
const form = useForm({
    from: firstOfMonth,
    until: date
});
const dialog = useDialog();
const toast = useToast();

function search() {
    form
        .transform((data) => {
            const dateFormat = 'yyyy-MM-dd';
            return {
                from: format(data.from, dateFormat),
                until: format(data.until, dateFormat)
            }
        })
        .get(route('quotes.index'), {
        only: ['quotes'],
        preserveState: true
    });
}

function openConfirmationDialog(config: ConfirmationDialogConfig) {
    const dialogInstance = dialog.open(ConfirmationDialog, {
        props: {
            modal: true,
            draggable: false,
            header: config.header,
        },
        data: {
            body: config.body,
        },
        emits: {
            onConfirm() {
                const options = {
                    onSuccess() {
                        toast.add({
                            detail: config.successMessage,
                            severity: 'success',
                            life: 3000
                        });
                        dialogInstance.close();
                    }
                };

                if (config.method === 'delete') {
                    router.delete(config.route, options);
                    return;
                }

                router.post(config.route, undefined, options);
            }
        }
    });
}

function checkSelection(): Boolean {
    if (!selection.value) {
        toast.add({
            detail: 'Debe seleccionar un elemento.',
            severity: 'warn',
            life: 3000
        });
        return false;
    }

    return true;
}

defineExpose({
    openConfirmationDialog,
    checkSelection,
    selection: computed(() => selection.value)
});
</script>

<template>
    <DataTable
        v-model:filters="filters"
        v-model:selection="selection"
        :value
        selection-mode="single"
        data-key="id"
        paginator
        scrollable
        :rows="10"
        :global-filter-fields="globalFilters"
        pt:root:class="flex flex-col overflow-hidden w-full flex-grow"
        pt:table-container="flex-grow bg-white"
    >
        <template #header>
            <Toolbar class="mb-2">
                <template #start>
                    <RangeDatePicker v-model:start="form.from" v-model:end="form.until"/>
                    <Button icon="fa-solid fa-magnifying-glass" label="Buscar" @click="search" class="ml-4"/>
                </template>
            </Toolbar>
            <Menubar :model="menuItems">
                <template v-if="globalFilters" #end>
                    <IconField>
                        <InputIcon class="fa-solid fa-magnifying-glass"/>
                        <InputText v-model="filters.global.value"/>
                    </IconField>
                </template>
            </Menubar>
        </template>
        <slot/>
    </DataTable>
</template>
