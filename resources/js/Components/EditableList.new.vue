<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref } from 'vue';

interface Props {
    value: any[];

}
const props = defineProps<Props>();
const editingRows = ref([{number: 2}]);
</script>

<template>
<DataTable
    :value
    edit-mode="row"
    v-model:editing-rows="editingRows">
    {{ editingRows }}
    <slot/>
    <Column row-editor>
        <template #roweditoriniticon>
            <i class="fa-solid fa-pen fa-lg"/>
        </template>
        <template #roweditorsaveicon>
            <i class="fa-solid fa-floppy-disk fa-lg"/>
        </template>
        <template #roweditorcancelicon>
            <i class="fa-solid fa-xmark fa-lg"/>
        </template>
    </Column>
</DataTable>
</template>