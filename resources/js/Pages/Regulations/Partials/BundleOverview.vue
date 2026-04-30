<script setup lang="ts">
import { useTree } from '@/Components/Tree/useTree';
import Card from 'primevue/card';
import { TreeNode } from 'primevue/treenode';
import { currency } from '@/utils/formatters';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Toolbar from 'primevue/toolbar';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api'
import axios from 'axios';

const { selection, pathToSelection } = useTree('reg_tree');
const path = pathToSelection.value;
const { label, regulation } = path.find((node: TreeNode) => node.type === 'regulation') as TreeNode;
const bundledParameters = ref(selection.value?.bundle?.parameters);
const filters = ref({ name: { value: null, matchMode: FilterMatchMode.STARTS_WITH } });

function save() {
    if (!selection.value) return;

    const bundle = bundledParameters.value.map((param: { id: number }) => param.id);
    axios.post(route('regulations.bundles.package', selection.value?.bundle.id), { bundle })
        .then(({data}) => {
            if (selection.value) {
                selection.value.bundle.parameters = data;
            }
        });
}
</script>

<template>
    <div class="flex flex-col h-full gap-3">
        <Card>
            <template #title>{{ label }}</template>
            <template #content>
                <div class="flex gap-3">
                    <div>
                        <div>Nombre</div>
                        <div>Precio</div>
                        <div>Tomas</div>
                    </div>
                    <div>
                        <div>{{ selection?.label }}</div>
                        <div>{{ currency(selection?.bundle?.price) }}</div>
                        <div>{{ selection?.bundle?.takes }}</div>
                    </div>
                </div>
            </template>
        </Card>
        <Card
            pt:root="overflow-hidden"
            pt:body="overflow-hidden"
            pt:content="overflow-hidden">
            <template #content>
                <DataTable
                    v-model:selection="bundledParameters"
                    v-model:filters="filters"
                    data-key="id"
                    scrollable
                    scroll-height="flex"
                    paginator
                    :rows="12"
                    :value="regulation.parameters">
                    <template #header>
                        <Toolbar>
                            <template #start>
                                <IconField>
                                    <InputIcon class="fa-solid fa-magnifying-glass"/>
                                    <InputText v-model="filters.name.value" placeholder="Buscar parámetero..."/>
                                </IconField>
                            </template>
                            <template #end>
                                <Button
                                    label="Guardar"
                                    icon="fa-solid fa-floppy-disk"
                                    @click="save"/>
                            </template>
                        </Toolbar>
                    </template>
                    <Column selection-mode="multiple"/>
                    <Column header="Parámetro" field="name"/>
                    <Column header="Área" field="area"/>
                </DataTable>
            </template>
        </Card>
    </div>
</template>