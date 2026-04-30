import { DynamicDialogInstance } from 'primevue/dynamicdialogoptions';
import { ComputedRef } from 'vue';

interface DialogRef<T> extends Omit<ComputedRef, 'value'> {
    value: Omit<DynamicDialogInstance, 'data'> & {
        data: T;
    }
}