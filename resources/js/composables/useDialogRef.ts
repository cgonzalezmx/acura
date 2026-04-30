import { DialogRef } from '@/types/dialogRef';
import { inject } from 'vue';

export function useDialogRef<T>() {
    return inject<DialogRef<T>>('dialogRef')!;
}
