<script setup lang="ts">
import { computed, defineComponent, reactive, ref, resolveDirective, SetupContext, withDirectives } from 'vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import { h } from 'vue';

interface Item {
    id: number;
    [key: string]: any;
}

const emit = defineEmits(['store', 'update', 'delete']);
defineProps<{
    title: string,
    items?: Item[],
}>();

const isEditing = computed(() => editingState.mode !== 'none');
const warnUser = ref(false);
const editingState = reactive({
    mode: 'none' as 'none' | 'add' | 'edit',
    index: -1,
    item: { id: -1 } as Item
});
const isEditingIndex = (index: number) => editingState.index === index && isEditing.value;

function select(index: number, item: Item) {
    editingState.index = index;
    editingState.item = item;
}

function disableEdition() {
    editingState.index = -1;
    editingState.item = { id: -1 };
    editingState.mode = 'none'
}
 
function editItem(index: number, item: any) {
    editingState.mode = 'edit';
    select(index, {...item});
}

function cancel() {
    disableEdition();
}

function saveItem() {
    const action = editingState.mode === 'add' ? 'store' : 'update';
    emit(action, editingState.item);
    disableEdition();
}

function selectForDeletion(index: number, item: Item) {
    select(index, {...item});
    warnUser.value = true;
}

function deleteItem() {
    emit('delete', editingState.item);
    warnUser.value = false;
}

const ListField = (props: any, context: SetupContext) => {
    return h('div', { class: 'flex border-b-slate-200 mb-2 last-of-type:border-none' }, [
        h('div', { class: 'p-2 flex flex-1' }, h('div', { class: 'w-full' }, context.slots?.start?.())),
        h('div', { class: 'flex flex-col flex-1 gap-3' }, context.slots?.middle?.()),
        h('div', { class: 'flex gap-1' }, context.slots?.end?.()),
    ])
}

const IconButton = defineComponent({
    props: ['tooltip'],
    render() {
        return withDirectives(h(Button, { class: 'self-center' }), [
            [ resolveDirective('tooltip'), {
                value: this.$props.tooltip,
                showDelay: 1000,
                hideDelay: 300
            }, undefined, { top: true }]
        ]);
    }
});
</script>

<template>
    <div class="flex flex-col h-full">
        <div class="p-4 border-b-2 border-b-primary-500">
            <span class="text-2xl font-semibold">
                {{ title }}
            </span>
        </div>
        <div class="p-4 overflow-y-auto overflow-x-hidden relative">
            <ListField v-if="editingState.mode === 'add'">
                <template #start>
                    <slot
                        name="edition"
                        :currentItem="editingState.item"
                        :index="-1"/>
                </template>
                <template #end>
                    <IconButton
                        tooltip="Guardar"
                        icon="fa-regular fa-floppy-disk"
                        @click="saveItem"/>
                    <IconButton
                        tooltip="Cancelar"
                        icon="fa-solid fa-xmark"
                        severity="secondary"
                        @click="cancel"/>
                </template>
            </ListField>
            <ListField v-for="(item, index) in items" :key="item.id">
                <template #start>
                    <slot
                        v-if="isEditingIndex(index)"
                        name="edition"
                        :currentItem="editingState.item"
                        :index="editingState.index"/>
                    <slot
                        v-else
                        name="display"
                        :item/>
                </template>
                <template #middle>
                    <slot name="summary" :item/>
                </template>
                <template #end>
                    <template v-if="isEditingIndex(index)">
                        <IconButton
                            tooltip="Guardar"
                            icon="fa-regular fa-floppy-disk"
                            @click="saveItem"/>
                        <IconButton
                            tooltip="Cancelar"
                            icon="fa-solid fa-xmark"
                            severity="secondary"
                            @click="cancel"/>
                    </template>
                    <template v-else>
                        <IconButton
                            tooltip="Editar"
                            icon="fa-solid fa-pen"
                            :disabled="isEditing"
                            @click="editItem(index, item)"/>
                        <IconButton
                            tooltip="Eliminar"
                            icon="fa-regular fa-trash-can"
                            outlined
                            :disabled="isEditing"
                            severity="danger"
                            @click="selectForDeletion(index, item)"/>
                    </template>
                </template>
            </ListField>
            <Button
                label="Agregar"
                icon="fa-solid fa-plus"
                :disabled="editingState.mode !== 'none'"
                raised
                @click="editingState.mode = 'add'"
                class="sticky bottom-0"/>
        </div>
    </div>
    <Dialog v-model:visible="warnUser" modal header="Advertencia">
        <div class="flex flex-col">
            <div>
                Está a punto de eliminar el registro.
            </div>
            <div
                v-if="$slots['deletion-warning']"
                class="mb-5">
                <slot name="deletion-warning" :currentItem="editingState.item" />
            </div>
            <div class="flex gap-3">
                <Button
                    label="Borrar"
                    severity="danger"
                    @click="deleteItem"/>
                <Button
                    label="Cancelar"
                    severity="secondary"
                    @click="warnUser = false"/>
            </div>
        </div>
    </Dialog>
</template>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(3em);
}

.list-leave-active {
    position: absolute;
}
</style>