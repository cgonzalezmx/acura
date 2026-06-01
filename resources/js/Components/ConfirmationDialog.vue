<script setup lang="ts">
import Button from 'primevue/button';
import { DynamicDialogInstance } from 'primevue/dynamicdialogoptions';
import { inject, Ref } from 'vue';

interface DialogRef extends DynamicDialogInstance {
    data: {
        body: string;
    }
}

defineEmits(['confirm']);
const dialogRef = inject<Ref<DialogRef>>('dialogRef');
</script>

<template>
    <div>
        <div class="mb-4">
            <template v-for="text in dialogRef?.data.body">
                <p>{{ text }}</p>
            </template>
        </div>
        <div>
            <Button label="Confirmar" class="mr-4" @click="$emit('confirm')"/>
            <Button label="Cancelar" severity="danger" outlined @click="dialogRef?.close()"/>
        </div>
    </div>
</template>
